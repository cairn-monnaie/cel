<?php                                                                          
// src/Cairn/UserBundle/Service/Commands.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Cairn\UserBundle\Service\MessageNotificator;

//UserBundle Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;

use Cairn\UserCyclosBundle\Entity\UserManager;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;

use Cyclos;

class Commands
{
    protected $em;

    protected $messageNotificator;

    protected $templating;

    protected $cardActivationDelay;

    protected $emailValidationDelay;

    protected $router;

    protected $container;

    public function __construct(EntityManager $em, MessageNotificator $messageNotificator, TwigEngine $templating, $cardActivationDelay, $emailValidationDelay, Router $router, Container $container)
    {
        $this->em = $em;
        $this->messageNotificator = $messageNotificator;
        $this->templating = $templating;
        $this->cardActivationDelay = $cardActivationDelay;
        $this->emailValidationDelay = $emailValidationDelay;
        $this->router = $router;
        $this->userManager = new UserManager();
        $this->container = $container;
    }

    public function updateOperations()
    {
        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');

        $ob = $operationRepo->createQueryBuilder('o');                 
        $scheduledFailedTransactions = $ob->where('o.paymentID is NULL')                      
            ->getQuery()->getResult();

        foreach($scheduledFailedTransactions as $transaction){
            $this->em->remove($transaction);
        }

        $this->em->flush();
    }


    /**
     *Returns true and creates admin if he does not exist yet, returns false otherwise
     *
     *@return boolean 
     */ 
    public function createInstallAdmin($username, $password)
    {

        $userRepo = $this->em->getRepository('CairnUserBundle:User');
        $ub = $userRepo->createQueryBuilder('u')
            ->where('u.username = :username')
            ->setParameter('username',$username);
        $userRepo->whereRole($ub,'ROLE_SUPER_ADMIN');
        $main_admin = $ub->getQuery()->getOneOrNullResult();

        if (!$main_admin){

            //get cyclos reference
            $credentials = array('username'=>$username,'password'=>$password);

            $network = $this->container->getParameter('cyclos_network_cairn');
            $group = $this->container->getParameter('cyclos_group_network_admins');

            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($network,'login',$credentials);

            try{
                $userVO = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($username);

                $isInAdminGroup = $this->container->get('cairn_user_cyclos_user_info')->isInGroup($group ,$userVO->id);

                if(!$isInAdminGroup){
                    return 'This user can\'t be installed as an admin in the application : not in group '.$group;
                }
            }catch(Cyclos\ServiceException $e){
                if($e->errorCode == 'LOGIN'){
                    return 'Wrong username or password provided';
                }else{
                    throw $e;
                }
            }   

            $id = $userVO->id;
            $userData = $this->container->get('cairn_user_cyclos_user_info')->getProfileData($id);

            $new_admin = new User();
            $new_admin->setUsername($username);
            $new_admin->setName($userData->name);
            $new_admin->setEmail($userData->email);
            $new_admin->setCyclosID($id);


            $new_admin->setPlainPassword($password);
            $new_admin->setEnabled(true);

            $new_admin->addRole('ROLE_SUPER_ADMIN');

            $zip = $this->em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('zipCode'=>'38000','city'=>'Grenoble'));
            $address = new Address();
            $address->setZipCity($zip);
            $address->setStreet1('7 rue Très Cloîtres');

            $new_admin->setAddress($address);
            $new_admin->setDescription('Administrateur de l\'application');


            //ajouter la carte
            $salt = $this->container->get('cairn_user.security')->generateCardSalt($new_admin);
            $card = new Card($new_admin,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),$salt);
            $new_admin->setCard($card);
            $this->em->persist($new_admin);

            //set admin has referent of all users including himself
            $allUsers = $userRepo->findAll();

            $new_admin->addReferent($new_admin);
            foreach($allUsers as $user){
                $user->addReferent($new_admin);
            }

            $this->em->flush();

            return 'admin user has been created successfully !';
        }
        return 'admin user has already been created !';
    }

    /**
     * searches new registered users whom emails have not been confirmed, warns them or remove them
     *
     * Everyday, this action is requested to look for registered users who have not validated their email. A delay to do so is defined.
     * If the deadline is missed, the new registered user is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     */
    public function checkEmailsConfirmation()
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $ub = $userRepo->createQueryBuilder('u');
        $ub->where('u.enabled = false')
            ->andWhere('u.lastLogin is NULL')
            ->andWhere('u.confirmationToken is not NULL')
            ;

        $pendingUsers = $ub->getQuery()->getResult();

        $from = $this->messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));

        foreach($pendingUsers as $user){
            $creationDate = $user->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ '.$this->emailValidationDelay.' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->emailValidationDelay,30);
            if( ($interval->invert == 0) && ($diff != 0)){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Validation de votre adresse email';
                        $confirmationUrl = $this->router->generate('fos_user_registration_confirm',
                            array('token'=>$user->getConfirmationToken()) );

                        $body = $this->templating->render('CairnUserBundle:Emails:reminder_email_activation.html.twig',
                            array('email'=>$user->getEmail(),'remainingDays'=>$diff,'confirmationUrl'=>$confirmationUrl));

                        $this->messageNotificator->notifyByEmail($subject,$from,$user->getEmail(),$body);

                    }
                }
            }
            else{
                $subject = 'Confirmation de mot de passe expirée';
                $body = $this->templating->render('CairnUserBundle:Emails:email_expiration.html.twig');

                $saveEmail = $user->getEmail();

                //the user cannot be removed on Symfony and Cyclos side as there is no user connected to request this command (this is 
                //a command to activate regularly
                $user->setRemovalRequest(true);

                //the user won't be able anymore to confirm his email
                $user->setConfirmationToken(NULL);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);

            }

        }
    }

    /**
     * searches users with unactivated cards, warns them or remove their card
     *
     * Everyday, this action is requested to look for users who have not activated their card. A maximal delay is defined.
     * If the deadline is missed, the new user's card is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     */
    public function checkCardsActivation()
    {
        $cardRepo = $this->em->getRepository('CairnUserBundle:Card');

        $cb = $cardRepo->createQueryBuilder('c');
        $cb->join('c.user','u')
            ->where('c.enabled = false')
            ->andWhere('c.generated = true')
            ->addSelect('u');
        $cards = $cb->getQuery()->getResult();

        $from = $this->messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));
        foreach($cards as $card){
            $creationDate = $card->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ '.$this->cardActivationDelay.' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->cardActivationDelay,30);
            if( ($interval->invert == 0) && ($diff != 0)){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Activation de votre carte de sécurité Cairn';
                        $body = $this->templating->render('CairnUserBundle:Emails:reminder_card_activation.html.twig',array('card'=>$card,'remainingDays'=>$diff));
                        $this->messageNotificator->notifyByEmail($subject,$from,$card->getUser()->getEmail(),$body);

                    }

                }
            }
            else{
                $subject = 'Expiration de votre carte de sécurité Cairn';
                $body = $this->templating->render('CairnUserBundle:Emails:expiration_card.html.twig');
                $card->getUser()->setCard(NULL);
                $saveEmail = $card->getUser()->getEmail();
                $this->em->remove($card);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);
            }
        }
    }

    public function createUser($cyclosUser, $admin)
    {
        $doctrineUser = new User();

        $cyclosUserData = $this->container->get('cairn_user_cyclos_user_info')->getProfileData($cyclosUser->id);

        $doctrineUser->setCyclosID($cyclosUserData->id);                                      
        $doctrineUser->setUsername($cyclosUserData->username);                           
        $doctrineUser->setName($cyclosUserData->name);
        $doctrineUser->setEmail($cyclosUserData->email);
        $doctrineUser->setFirstLogin(false);

        $creationDate = new \Datetime($cyclosUserData->activities->userActivationDate);
        $doctrineUser->setCreationDate($creationDate);
        $doctrineUser->setPlainPassword('@@bbccdd');                      
        $doctrineUser->setEnabled(true);                                      

        if($cyclosUserData->group->nature == 'MEMBER_GROUP'){
            $doctrineUser->addRole('ROLE_PRO');   
        }else{
            $doctrineUser->addRole('ROLE_ADMIN');   
        }                

        $cyclosAddress = $cyclosUserData->addressListData->addresses[0];
        $zip = $this->em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('city'=>$cyclosAddress->city));
        $address = new Address();                                          
        $address->setZipCity($zip);                                        
        $address->setStreet1($cyclosAddress->addressLine1);

        $doctrineUser->setAddress($address);                                  
        $doctrineUser->setDescription('Test user blablablabla');             

        $card = new Card($doctrineUser,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),'aaaa');
        $fields = $card->generateCard($this->container->getParameter('kernel.environment'));

        //encode user's card
        $this->container->get('cairn_user.security')->encodeCard($card);
        $card->setGenerated(true);
        $doctrineUser->setCard($card);
        $doctrineUser->addReferent($admin);
        $this->em->persist($doctrineUser);

    }

    /**
     * Here we create an operation and its aborted copy (paymentID is NULL)
     */
    public function createOperation($entryVO, $type)
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $bankingService = $this->container->get('cairn_user_cyclos_banking_info');

        if($type == Operation::TYPE_DEPOSIT || $type == Operation::TYPE_TRANSACTION_EXECUTED){
            $dueDate = $entryVO->date;
            $transactionVO = $bankingService->getTransactionByID($entryVO->id);
        }else{
            $dueDate = $entryVO->dueDate;
            $transactionVO = $bankingService->getTransactionByID($entryVO->scheduledPayment->id);
        }

        $operation = new Operation();
        $operation->setType($type);
        $operation->setPaymentID($transactionVO->id);
        $operation->setAmount($transactionVO->currencyAmount->amount);
        $operation->setReason('Motif du virement de test');
        $operation->setDescription($transactionVO->description);
        $operation->setExecutionDate(new \Datetime($dueDate));

        $debitorAccountVO = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($transactionVO->fromOwner);
        $operation->setFromAccountNumber($debitorAccountVO->number);

        $creditorAccountVO = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($transactionVO->toOwner);
        $stakeholder = $userRepo->findOneByUsername($transactionVO->toOwner->shortDisplay);
        $operation->setToAccountNumber($creditorAccountVO->number);
        $operation->setStakeHolder($stakeholder);

        $abortedOperation = Operation::copyFrom($operation);

        $this->em->persist($operation);
        $this->em->persist($abortedOperation);

    }

    public function generateDatabaseFromCyclos($login, $password)
    {
        //same username than the one provided at installation
        $adminUsername = $login;
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $users = $userRepo->myFindByRole(array('ROLE_PRO'));

        if(!$users){
            $credentials = array('username'=>$adminUsername,'password'=>$password);
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),'login',$credentials);

            //generate doctrine users 
            $memberGroupName = $this->container->getParameter('cyclos_group_pros');
            $adminGroupName = $this->container->getParameter('cyclos_group_network_admins');

            try{
                $memberGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($memberGroupName ,'MEMBER_GROUP');
                $cyclosMembers = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($memberGroup->id);

//                $adminGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($adminGroupName ,'ADMIN_GROUP');
//                $cyclosAdmins = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($adminGroup->id);
//
//                $cyclosUsers = array_merge($cyclosMembers,$cyclosAdmins);
            }catch(Cyclos\ServiceException $e){
                if($e->errorCode == 'LOGIN'){
                    return 'Wrong username or password provided';
                }else{
                    throw $e;
                }
            }   

            $admin = $userRepo->findOneByUsername('admin_network');

            //basic user creation : create entity using data from Cyclos + add a card for all users
            foreach($cyclosMembers as $cyclosUser){
                $this->createUser($cyclosUser,$admin);
            }

            $this->em->flush();

            ////// payments creation : foreach deposit and scheduled payment on cyclos side, we create here a Doctrine equivalent
            $bankingService = $this->container->get('cairn_user_cyclos_banking_info');
            $accountTypeVO = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes(NULL,'USER')[0];


            //instances of TransactionEntryVO
            $processedDeposits = $bankingService->getTransactions(
                $admin->getCyclosID(),$accountTypeVO->id,array('PAYMENT','SCHEDULED_PAYMENT'),array('PROCESSED',NULL,'CLOSED'),'dépôt');

            foreach($processedDeposits as $transaction){
                $this->createOperation($transaction,Operation::TYPE_DEPOSIT);
            }

            //instances of TransactionEntryVO
            //in init_data_test.py script, trankilou makes transaction in order to have a null account balance
            $user = $userRepo->findOneByUsername('trankilou'); 

            $processedTransactions = $bankingService->getTransactions(
                $user->getCyclosID(),$accountTypeVO->id,array('PAYMENT'),array('PROCESSED',NULL,'CLOSED'),'remise à 0');

            foreach($processedTransactions as $transaction){
                $this->createOperation($transaction,Operation::TYPE_TRANSACTION_EXECUTED);
            }

            //instances of ScheduledPaymentInstallmentEntryVO (these are actually installments, not transfers yet)
            //the id used to execute an operation on this installment is from an instance of ScheduledPaymentEntryVO
            //in init_data_test.py script, future transactions are made by labonnepioche
            $user = $userRepo->findOneByUsername('labonnepioche'); 

            $credentials = array('username'=>'labonnepioche','password'=>$password);
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),'login',$credentials);

            $futureInstallments = $bankingService->getInstallments($user->getCyclosID(),$accountTypeVO->id,array('BLOCKED','SCHEDULED'),'virement futur');

            $credentials = array('username'=>'admin_network','password'=>$password);
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),'login',$credentials);

            var_dump(count($futureInstallments));
            
            foreach($futureInstallments as $installment){
                $this->createOperation($installment,Operation::TYPE_TRANSACTION_SCHEDULED);
            }

//********* Here, we set specific attributes to each user in order to test different contexts and data *******************************

            //admin has a generated and validated card too, by default
            $admin->setFirstLogin(false);
            $admin->getCard()->generateCard($this->container->getParameter('kernel.environment'));
            $this->container->get('cairn_user.security')->encodeCard($admin->getCard());
            $admin->getCard()->setGenerated(true);
            $admin->getCard()->setEnabled(true);

            //vie_integrative has generated(default at user creation in createUser function) and validated card + admin is not referent
            $user = $userRepo->findOneByUsername('vie_integrative'); 
            $user->removeReferent($admin);
            $card  = $user->getCard();
            $card->setEnabled(true);

            //users have generated(default at user creation in createUser function) and validated card
            $user = $userRepo->findOneByUsername('labonnepioche'); 
            $card  = $user->getCard();
            $card->setEnabled(true);

            $user = $userRepo->findOneByUsername('lib_colibri'); 
            $card  = $user->getCard();
            $card->setEnabled(true);

            //card is not generated
            $user = $userRepo->findOneByUsername('DrDBrew'); 
            $card  = $user->getCard();
            $card->setGenerated(false);

            //episol has NO card
            $user = $userRepo->findOneByUsername('episol'); 
            $card  = $user->getCard();
            $this->em->remove($card);

            //NaturaVie has NO card and admin not referent
            $user = $userRepo->findOneByUsername('NaturaVie'); 
            $user->removeReferent($admin);
            $card  = $user->getCard();
            $this->em->remove($card);

            //nico_faus_prod has beneficiary labonnepioche
            $debitor = $userRepo->findOneByUsername('nico_faus_prod'); 
            $debitor->getCard()->setEnabled(true);
            $creditor = $userRepo->findOneByUsername('labonnepioche'); 

            $benef = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($creditor->getCyclosID());
            $ICC = $benef->number;
            $beneficiary = new Beneficiary();
            $beneficiary->setICC($ICC);
            $beneficiary->setUser($creditor);
            $debitor->addBeneficiary($beneficiary);
            $beneficiary->addSource($debitor);

            //le_marque_page has beneficiary labonnepioche
            $debitor = $userRepo->findOneByUsername('le_marque_page'); 
            $debitor->addBeneficiary($beneficiary);
            $beneficiary->addSource($debitor);

            //pain_beauvoir has beneficiary ferme_bressot
            $debitor = $userRepo->findOneByUsername('pain_beauvoir'); 
            $creditor = $userRepo->findOneByUsername('ferme_bressot'); 

            $benef = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($creditor->getCyclosID());
            $ICC = $benef->number;
            $beneficiary = new Beneficiary();
            $beneficiary->setICC($ICC);
            $beneficiary->setUser($creditor);

            $debitor->addBeneficiary($beneficiary);
            $beneficiary->addSource($debitor);

            //user has requested a removal and has null account balance on Cyclos-side
            $user = $userRepo->findOneByUsername('Biocoop'); 
            $user->setRemovalRequest(true);

            //user has requested a removal and has non-null account balance on Cyclos-side
            $user = $userRepo->findOneByUsername('Alpes_EcoTour'); 
            $user->setRemovalRequest(true);

            //user is blocked
            $user = $userRepo->findOneByUsername('tout_1_fromage'); 
            $user->setEnabled(false);

//            //users have ROLE_ADMIN as referent
//            $user1 = $userRepo->findOneByUsername('atelier_eltilo'); 
//            $user2 = $userRepo->findOneByUsername('mon_vrac'); 
//
//            $admin1 = $userRepo->findOneByUsername('gl_grenoble'); 
//            $admin2  = $userRepo->findOneByUsername('gl_voiron'); 
//
//            $user1->addReferent($admin1);
//            $user2->addReferent($admin2);

            $this->em->flush();

            return 'Database successfully generated !';
        }else{
            return 'The database is not empty ! It can\'t be generated';
        }

    }
}

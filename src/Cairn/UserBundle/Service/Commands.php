<?php                                                                          
// src/Cairn/UserBundle/Service/Commands.php                             

namespace Cairn\UserBundle\Service;                                      

use Symfony\Bundle\TwigBundle\TwigEngine;
use Doctrine\ORM\EntityManager;
use Cairn\UserBundle\Service\MessageNotificator;

//UserBundle Entities
use Cairn\UserBundle\Entity\User;
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
        $scheduledTransactions = $ob->where('o.paymentID is not NULL')                      
            ->andWhere('o.executionDate <= :date')                     
            ->andWhere('o.type = :type')                               
            ->setParameter('date',new \Datetime())                     
            ->setParameter('type',Operation::$TYPE_TRANSACTION_SCHEDULED)
            ->orderBy('o.executionDate','ASC')                         
            ->getQuery()->getResult();

        foreach($scheduledTransactions as $transaction){
            $scheduledPaymentVO = $this->container->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosOperation($transaction);
            if($scheduledPaymentVO->installments[0]->status == 'FAILED'){
                $transaction->setType(Operation::$TYPE_SCHEDULED_FAILED);
            }elseif($scheduledPaymentVO->installments[0]->status == 'PROCESSED'){
                $transaction->setType(Operation::$TYPE_TRANSACTION_EXECUTED);
            }

        }

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
                    return 'This user can\'t be installed as an admin in the application';
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

            //set auto-referent
            $new_admin->addReferent($new_admin);


            //ajouter la carte
            $salt = $this->container->get('cairn_user.security')->generateCardSalt($new_admin);
            $card = new Card($new_admin,$this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),$salt);
            $new_admin->setCard($card);


            $this->em->persist($new_admin);
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
    public function checkEmailsValidation()
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
                $subject = 'Expiration de validation';
                $body = $this->templating->render('CairnUserBundle:Emails:email_expiration.html.twig',array('diff'=>$diff));

                $saveEmail = $user->getEmail();
                $this->em->remove($user);
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
                $body = $this->templating->render('CairnUserBundle:Emails:expiration_card.html.twig',array('card'=>$card,'diff'=>$diff));
                $card->getUser()->setCard(NULL);
                $saveEmail = $card->getUser()->getEmail();
                $this->em->remove($card);
                $this->em->flush();
                $this->messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);
            }
        }
    }

    public function createUser($cyclosUser)
    {
        $doctrineUser = new User();

        $cyclosUserData = $this->container->get('cairn_user_cyclos_user_info')->getProfileData($cyclosUser->id);

        $doctrineUser->setCyclosID($cyclosUserData->id);                                      
        $doctrineUser->setUsername($cyclosUserData->username);                           
        $doctrineUser->setName($cyclosUserData->name);
        $doctrineUser->setEmail($cyclosUserData->email);
        $doctrineUser->isFirstLogin(false);

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
        $doctrineUser->setCard($card);

        $this->em->persist($doctrineUser);

    }

    public function generateDatabaseFromCyclos($login, $password)
    {
        //same username than the one provided at installation
        $adminUsername = $login;
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $users = $userRepo->findAll();

        if(!$users){
            $credentials = array('username'=>$adminUsername,'password'=>$password);
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),'login',$credentials);

            //generate doctrine users 
            $memberGroupName = $this->container->getParameter('cyclos_group_pros');
            $adminGroupName = $this->container->getParameter('cyclos_group_network_admins');

            try{
                $memberGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($memberGroupName ,'MEMBER_GROUP');
                $cyclosMembers = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($memberGroup->id);
            }catch(Cyclos\ServiceException $e){
                if($e->errorCode == 'LOGIN'){
                    return 'Wrong username or password provided';
                }else{
                    throw $e;
                }
            }   

            //basic user creation : create entity using data from Cyclos + add a card for all users
            foreach($cyclosMembers as $cyclosMember){
                $this->createUser($cyclosMember);
            }
            $this->em->flush();

            //here, we set specific elements for testing different contexts and data

            //card is generated and is validated
            $user = $userRepo->findOneByUsername('vie_integrative'); 
            $card  = $user->getCard();
            $card->generateCard('test');
            $card->setGenerated(true);
            $card->setEnabled(true);

            $user = $userRepo->findOneByUsername('labonnepioche'); 
            $card  = $user->getCard();
            $card->generateCard('test');
            $card->setGenerated(true);
            $card->setEnabled(true);

            //card is generated and is NOT validated
            $user = $userRepo->findOneByUsername('recycleco'); 
            $card  = $user->getCard();
            $card->generateCard('test');
            $card->setGenerated(true);
            $card->setEnabled(false);

            //user has NO card
            $user = $userRepo->findOneByUsername('episol'); 
            $card  = $user->getCard();
            $this->em->remove($card);


            return 'Database successfully generated !';
        }else{
            return 'The database is not empty ! It can\'t be generated';
        }

    }
}

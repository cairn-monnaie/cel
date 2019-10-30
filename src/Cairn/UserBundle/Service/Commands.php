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
use Cairn\UserBundle\Entity\File;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\NotificationPermission;
use Cairn\UserBundle\Entity\HelloassoConversion;
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Entity\Sms;
use Cairn\UserBundle\Entity\Mandate;
use Cairn\UserBundle\Entity\AccountScore;


use Knp\Snappy\Pdf;
use Cairn\UserCyclosBundle\Entity\UserManager;

use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

use Cyclos;

class Commands
{
    protected $em;

    protected $messageNotificator;

    protected $templating;

    protected $cardAssociationDelay;

    protected $emailValidationDelay;

    protected $router;

    protected $container;

    public function __construct(EntityManager $em, MessageNotificator $messageNotificator, TwigEngine $templating,string $cardAssociationDelay,string $emailValidationDelay, Router $router, ContainerInterface $container)
    {
        $this->em = $em;
        $this->messageNotificator = $messageNotificator;
        $this->templating = $templating;
        $this->cardAssociationDelay = $cardAssociationDelay;
        $this->emailValidationDelay = $emailValidationDelay;
        $this->router = $router;
        $this->userManager = new UserManager();
        $this->container = $container;
    }

    public function generateLocalizationCoordinates($username = NULL)
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');
       
        if($username){
            $user = $userRepo->findOneByUsername($username);
            if(! $user){
                return 'username '.$username.' does not match any account';
            }
            if(! $user->isAdherent()){
                return 'username '.$username.' does not match any adherent account';
            }
            $users = array($user);
        }else{
            $ub = $userRepo->createQueryBuilder('u');                 
            $userRepo->whereAdherent($ub);

            $users = $ub->getQuery()->getResult();
        }

        $returnMsg = '';

        foreach($users as $user){
            $address = $user->getAddress();

            $coords = $this->container->get('cairn_user.geolocalization')->getCoordinates($address);

            if(!$coords['latitude']){                                  
                $returnMsg .= 'Echec de géolocalisation pour '.$username.' '.$user->getEmail()."\n".'Référence la plus pertinente: '.$coords['closest'];
            }else{                                         
                $address->setLongitude($coords['longitude']);
                $address->setLatitude($coords['latitude']);
                $returnMsg .= 'OK : '.$user->getUsername().' lat:'.$address->getLatitude().' lon:'.$address->getLongitude()."\n";
            }             
        }

        $this->em->flush();

        return $returnMsg;
    }

    /**
     * Removes all operations with no paymentID
     *
     * If an operation has no paymentID, it means that it has not been confirmed by the user
     *
     */
    public function removeAbortedOperations()
    {
        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');
        $smsRepo = $this->em->getRepository('CairnUserBundle:Sms');

        $ob = $operationRepo->createQueryBuilder('o');                 
        $scheduledAbortedTransactions = $ob->where('o.paymentID is NULL')                      
            ->getQuery()->getResult();

        foreach($scheduledAbortedTransactions as $transaction){
            $this->em->remove($transaction);
        }


        $this->em->flush();
    }

    public function sendAccountScores($username = NULL)
    {
        $now = new \Datetime();
        $dayNow = $now->format('D');
        $timeNow =  $now->format('H:i');
        $accountScoreRepo = $this->em->getRepository('CairnUserBundle:AccountScore');
        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $messageNotificator = $this->container->get('cairn_user.message_notificator');

        //how to get users based on their account score schedule
        $ab = $accountScoreRepo->createQueryBuilder('a');

        $ab->andWhere('a.confirmationToken is NULL')
            ->andWhere('a.consideredDay = :day')
            ->setParameter('day', $dayNow );

        if($username){
            $user = $userRepo->findOneByUsername($username);

            if(! $user){
                return 'User not found';
            }

            if(! $user->hasRole('ROLE_PRO')){
                return 'User must be a pro';
            }
            $ab->andWhere('a.user = :user')
                ->setParameter('user', $user);
        }

        $accountScores = $ab->getQuery()->getResult();

        foreach($accountScores as $accountScore){
            $schedule = $accountScore->getSchedule()[$dayNow];

            if( ! empty($schedule)){
                $nbToSend = count($schedule);
                $nbSent = $accountScore->getNbSentToday();

                if($nbSent < $nbToSend){
                    $filename = 'pointage-'.strtotime(date('Y-m-d H:i'));
                    $date = date('d-M-Y').' '.$schedule[$nbSent];
                    $dateToSend = new \Datetime($date);

                    //here we look for the timestamp of the last sent mail
                    if($nbSent != 0){ //if another account score was expected the same day
                        $beginTime = $schedule[$nbSent - 1];
                        $begin = new \Datetime($beginTime);
                        $begin->modify('+1 hour');

                    }else{ //otherwise, get the last timestamp of the previous day, if any
                        $clone = clone $now;
                        $dayBefore = $clone->modify('-1 day');

                        if( strtotime($dateToSend->format('H')) < strtotime('6:00') ){ // if  evening activity, must deal with day before
                            if($endYesterday = end($accountScore->getSchedule()[$dayBefore->format('D')]) ){
                                $begin = $dayBefore->modify($endYesterday)->modify('+1 hour');
                            }else{
                                $begin = $dayBefore->modify('15:00');
                            }
                        }else{ // daytime activity 
                            $cloneNow = clone $now;
                            $begin = $cloneNow->modify('00:00');
                        }

                                                
                    }

                    if( $now->diff($dateToSend)->invert == 1 ){
                        $ob = $operationRepo->createQueryBuilder('o');

                        $operationRepo
                            ->whereCreditor($ob, $accountScore->getUser())
                            ->whereSubmissionDateBetween($ob, $begin, $now);

                        $ob->andWhere('o.paymentID is not NULL')
                            ->andWhere( 
                                $ob->expr()->orX(
                                    'o.type = '.Operation::TYPE_SMS_PAYMENT
                                    ,
                                    'o.type = '.Operation::TYPE_MOBILE_APP
                                )
                            )
                            ->orderBy('o.executionDate','ASC');
                        $operations = $ob->getQuery()->getResult();

                        //generate Document
                        if($accountScore->getFormat() == 'csv'){ // CSV FORMAT
                            $filename = $filename.'.csv';
                            $handle = fopen($filename, 'w');

                            // Add the header of the CSV file
                            fputcsv($handle,array('Compte : ' . $accountScore->getUser()->getName()),';');
                            fputcsv($handle, array('Horodatage', 'Type','Montant'),';');

                            $totalAmount = 0;
                            foreach($operations as $operation ){

                                $totalAmount += $operation->getAmount();
                                fputcsv(
                                    $handle, // The file pointer
                                    array(
                                        $operation->getExecutionDate()->format('H:i'),
                                        Operation::getTypeName($operation->getType()),
                                        $operation->getAmount()
                                    ), // The fields
                                    ';' // The delimiter
                                );
                            }

                            fputcsv($handle,array('Somme cumulée : '.$totalAmount.' cairns'),';');
                            fputcsv($handle,array());

                            fclose($handle);

                            // You can alternatively use method chaining to build the attachment
                            $attachment = (new \Swift_Attachment())
                                ->setFilename($filename)
                                ->setContentType('application/force-download')
                                ->setBody(file_get_contents($filename))
                            ;

                            unlink($filename);
                        }else{ // PDF FORMAT
                            $totalAmount = 0;
                            foreach($operations as $operation ){
                                $totalAmount += $operation->getAmount();
                            }

                            $projectDir = $this->container->getParameter('kernel.project_dir');
 
                            $snappy = new Pdf($projectDir.'/vendor/h4cc/wkhtmltopdf-amd64/bin/wkhtmltopdf-amd64');
                            $html =  $this->container->get('templating')->render('CairnUserBundle:Pdf:account_score.html.twig',
                                array('operations'=>$operations,
                                'totalAmount'=>$totalAmount,
                                'beginDate'=>$begin,
                                'endDate'=>$now,
                                'accountScore'=>$accountScore
                                ));

                            $filename = $filename.'.pdf';

                            $attachment = (new \Swift_Attachment())
                                ->setFilename($filename)
                                ->setContentType('application/pdf')
                                ->setBody($this->container->get('knp_snappy.pdf')->getOutputFromHtml($html))
                            ;
                        }

                        $accountScore->setNbSentToday($accountScore->getNbSentToday() + 1);

                        $body = $this->container->get('templating')->render('CairnUserBundle:Emails:account_score.html.twig',array('accountScore'=>$accountScore,'begin'=>$begin));
                        $messageNotificator->notifyByEmail('Compta [e]-Cairn', $messageNotificator->getNoReplyEmail(), $accountScore->getEmail() ,$body, $attachment);
                    }
                }
            }else{//little bit of a hack to deal with side-effect but necessary
                $accountScore->setNbSentToday(0);
            }
        }

        $this->em->flush();

        return 'OK';
    }

    private function updateMandate(Mandate $mandate)
    {
        $accountManager = $this->container->get('cairn_user.account_manager');
        
        $status = $mandate->getStatus();
        if( !($status == Mandate::SCHEDULED || $status == Mandate::UP_TO_DATE || $status == Mandate::OVERDUE) ){
            return;
        }

        $today = new \Datetime();

        if($status == Mandate::SCHEDULED){
            if($mandate->getBeginAt()->diff($today)->invert == 0){
                $mandate->setStatus(Mandate::OVERDUE);
            }

        }elseif($status == Mandate::UP_TO_DATE){ //UP_TO_DATE
            if(! $accountManager->isUpToDateMandate($mandate)){
                $mandate->setStatus(Mandate::OVERDUE);
            }
        }else{  //OVERDUE
            if($accountManager->isUpToDateMandate($mandate)){
                $mandate->setStatus(Mandate::UP_TO_DATE);
            }

        }
    }

    /**
     * Each month, Updates status of ongoing and scheduled mandates
     *
     * If a mandate is ongoing, this command must check if there is an operation to operate or if everything is up to date
     */
    public function updateMandatesStatusCommand($username)
    {
        $userRepo = $this->em->getRepository('CairnUserBundle:User');
        $mandateRepo = $this->em->getRepository('CairnUserBundle:Mandate');

        $accountManager = $this->container->get('cairn_user.account_manager');

        if($username){
            $user = $userRepo->findOneByUsername($username);

            if(! $user){
                return 'User not found';
            }

            if(! $user->isAdherent()){
                return 'User must be an adherent';
            }

            $mb = $mandateRepo->createQueryBuilder('m');
            $mandateRepo->whereContractor($mb, $user)->whereStatus($mb, array(Mandate::UP_TO_DATE, Mandate::SCHEDULED,Mandate::OVERDUE));
            $mandates = $mb->getQuery()->getResult();

            if(! $mandates){
                return 'Aucun statut de mandat à mettre à jour';
            }

            foreach($mandates as $mandate){
                $this->updateMandate($mandate);
            }
                    
        }else{
            $mb = $mandateRepo->createQueryBuilder('m');
            $mandateRepo->whereStatus($mb, array(Mandate::UP_TO_DATE, Mandate::SCHEDULED,Mandate::OVERDUE));
            $mandates = $mb->getQuery()->getResult();

            foreach($mandates as $mandate){
                $this->updateMandate($mandate);
            }
        }

        $this->em->flush();

        return 'Mandates status updated';
    }

    /**
     *Returns true and creates admin if he does not exist yet, returns false otherwise
     *
     *@param string username : cyclos username of the admin
     *@param string password : cyclos password of the admin
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

            $network = $this->container->getParameter('cyclos_currency_cairn');
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
//            $new_admin->setMainICC($this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($id)->number);

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
            $this->em->persist($new_admin);

            //set admin as referent of all users including himself
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
     * searches unassociated cards, and removes them if the association delay has passed
     *
     * Everyday, this action is requested to look for unassociated cards. A maximal delay is defined.
     * If the deadline is missed, the card is automatically removed for security reasons : the card might have been lost
     *
     */
    public function checkCardsAssociation()
    {
        $cardRepo = $this->em->getRepository('CairnUserBundle:Card');

        $cb = $cardRepo->createQueryBuilder('c');

        $cardRepo->whereAvailable($cb)->whereExpiresBefore($cb,new \Datetime());
        
        $expiredCards = $cb->getQuery()->getResult();
                    
        foreach($expiredCards as $expiredCard){
            $this->em->remove($expiredCard);
        }

        $this->em->flush();

    }

    /**
     * Creates an user on Symfony side 
     *
     *@param int $rank  
     *@param stdClass $cyclosUser object representing user on cyclos-side
     *@param User $admin
     */
    public function createUser($cyclosUser, $admin, $rank)
    {
        $existingUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('cyclosID'=>$cyclosUser->id));

        if(!$existingUser){
            $doctrineUser = new User();
            $cyclosUserData = $this->container->get('cairn_user_cyclos_user_info')->getProfileData($cyclosUser->id);

            echo 'INFO: Creation de l\'utilisateur "' . $cyclosUserData->name . '" groupe("'. $cyclosUserData->group->name .'")'. "\n";

            $doctrineUser->setCyclosID($cyclosUserData->id);

            if($doctrineUser->isAdherent()){
                $doctrineUser->setMainICC($this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($cyclosUserData->id)->number);
            }
            $doctrineUser->setUsername($cyclosUserData->username);                           
            $doctrineUser->setName($cyclosUserData->name);
            $doctrineUser->setEmail($cyclosUserData->email);
            $doctrineUser->setFirstLogin(false);

            $doctrineUser->setCreationDate(new \Datetime());
            $doctrineUser->setLastLogin(new \Datetime());
            $doctrineUser->setPlainPassword('@@bbccdd');                      
            $doctrineUser->setEnabled(true);                                      

            if($cyclosUserData->group->name == $this->container->getParameter('cyclos_group_pros')){
                $doctrineUser->addRole('ROLE_PRO');   
            }elseif($cyclosUserData->group->name == $this->container->getParameter('cyclos_group_persons')){
                $doctrineUser->addRole('ROLE_PERSON');   
            }else{
                $doctrineUser->addRole('ROLE_ADMIN');   
            }                

            $cyclosAddress = $cyclosUserData->addressListData->addresses[0];
            $zip = $this->em->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('city'=>$cyclosAddress->city));
            $address = new Address();                                          
            $address->setZipCity($zip);                                        
            $address->setStreet1($cyclosAddress->addressLine1);

            $doctrineUser->setAddress($address);                                  
            $doctrineUser->setDescription('Je suis un compte de test !');             

            //create fake id doc
            $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
            $originalName = 'john-doe-id.png';
            $absolutePath = $absoluteWebDir.$originalName;

            $file = new UploadedFile($absolutePath,$originalName,null,null,null, true);

            $idDocument = new File();
            $idDocument->setUrl($file->guessExtension());
            $idDocument->setAlt($file->getClientOriginalName());

            if(! copy($absolutePath, $absoluteWebDir.$idDocument->getUploadDir().'/'.$rank.'.'.$idDocument->getUrl())){
                echo "Failed to copy";
            }

            $doctrineUser->setIdentityDocument($idDocument);


            $uniqueCode = $this->container->get('cairn_user.security')->findAvailableCode();
            $card = new Card($this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),'aaaa',$uniqueCode,$this->container->getParameter('card_association_delay'));
            $card->addUser($doctrineUser);
            $fields = $card->generateCard($this->container->getParameter('kernel.environment'));

            //encode user's card
            $this->container->get('cairn_user.security')->encodeCard($card,$doctrineUser);
            $doctrineUser->setCard($card);
            $doctrineUser->addReferent($admin);

            //set cyclos status to ACTIVE by default for adherents whereas, at creation, they are DISABLED
            //anonymous user will be the user accessing cyclos therefore, we need afterwards to reset admin credentials
            if($doctrineUser->isAdherent()){
                $doctrineUser->setMainICC($this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($cyclosUserData->id)->number);
                $this->container->get('cairn_user.access_platform')->changeUserStatus($doctrineUser, 'ACTIVE');

            }


            //each user can activate his access client after his status has changed from DISABLED to ACTIVE
            $credentials = array('username'=>$doctrineUser->getUsername(),'password'=>'@@bbccdd');
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

            $cyclosClient = $this->getClientToken($doctrineUser,'main');
            $doctrineUser->setCyclosToken($cyclosClient);

            $this->em->persist($doctrineUser);

            //in the end of the process, admin user will be up, as before, to request cyclos
            $credentials = array('username'=>'admin_network','password'=>'@@bbccdd');
            $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);
    
            echo 'INFO: OK !'. "\n";
        }

    }

    protected function getClientToken($user,$type)
    {
            $securityService = $this->container->get('cairn_user.security');      
            $securityService->createAccessClient($user,$type);  

            $accessClientVO = $this->container->get('cairn_user_cyclos_useridentification_info')->getAccessClientByUser($user->getCyclosID(),$type,'UNASSIGNED');
            $tokenClient = $securityService->changeAccessClientStatus($accessClientVO,'ACTIVE');

            return $securityService->vigenereEncode($tokenClient);
    }

    /**
     * Here, we setup Cyclos access clients for users with phone number
     * Then, we create aborted and EXPIRED SMS with these same phone numbers
     */
    public function setUpSmsAccessClient($user, $em)
    {
        echo 'Setting up access client for '.$user->getName()."\n";

        //changing cyclos credentials to user's instead of admins's is necessary to activate access client for himself
        $credentials = array('username'=>$user->getUsername(),'password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

        $smsData = $user->getSmsData();

        if($smsData){
            $smsClient = $this->getClientToken($user,'client_sms');
            $smsData->setSmsClient($smsClient);
        }

        $em->persist($user);

        $sms = new Sms($smsData->getPhones()[0]->getPhoneNumber(),'PAYER12BOOYASHAKA',Sms::STATE_EXPIRED,rand(0,25));

        $sms->setSentAt(date_modify( new \Datetime(), '-15 minutes'));
        $em->persist($sms);
        echo 'INFO: OK !'."\n";

        //in the end of the process, admin user will be up, as before, to request cyclos
        $credentials = array('username'=>'admin_network','password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

    }

    /**
     * Here we create an operation, its aborted copy (paymentID is NULL) 
     *
     * WARNING : The ID of the TransactionEntryVO object is not the same id than its corresponding transfer
     * For this reason, $entryVO->id is not what we want 
     *
     *@param const int $type Operation type
     *@param stdClass $entryVO stdClass TransactionEntryVO
     *@see https://documentation.cyclos.org/4.11.2/ws-api-docs/org/cyclos/model/banking/transactions/TransactionEntryVO.html
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

        echo $operation->getPaymentID();
        $operation->setAmount($transactionVO->currencyAmount->amount);
        $operation->setReason('Motif du virement de test');
        $operation->setDescription($transactionVO->description);
        $operation->setExecutionDate(new \Datetime($dueDate));

        $debitorAccountVO = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($transactionVO->fromOwner);
        $operation->setFromAccountNumber($debitorAccountVO->number);

        if($debitorAccountVO->type->nature == 'SYSTEM'){
            $debitor = $userRepo->myFindByRole(array('ROLE_SUPER_ADMIN'))[0];
        }else{
            $debitor = $userRepo->findOneByUsername($transactionVO->fromOwner->shortDisplay);
        }
        $operation->setDebitor($debitor);

        $creditorAccountVO = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($transactionVO->toOwner);
        $operation->setToAccountNumber($creditorAccountVO->number);

        if($creditorAccountVO->type->nature == 'SYSTEM'){
            $creditor = $userRepo->myFindByRole(array('ROLE_SUPER_ADMIN'))[0];
        }else{
            $creditor = $userRepo->findOneByUsername($transactionVO->toOwner->shortDisplay);
        }

        $operation->setCreditor($creditor);
        echo 'info: creation de l\'opération "'. Operation::getTypeName($type). ' from '.$operation->getDebitorName(). ' to '. $operation->getCreditorName(). "\n";

        $abortedOperation = Operation::copyFrom($operation);
        $abortedOperation->setPaymentID(NULL);

        $this->em->persist($operation);
        $this->em->persist($abortedOperation);

        echo 'INFO: OK !'. "\n";
    }

    public function createSmsData(User $user, $phoneNumber, $identifier = NULL)
    {
        $smsData = new SmsData($user);
        $nP = $smsData->getNotificationPermission();
        $nP->setEmailEnabled(false);
        $nP->setWebPushEnabled(true);
        $nP->setSmsEnabled(true);

        $phone1 = new Phone($smsData);
        $phone1->setPhoneNumber($phoneNumber);
        $phone1->setIdentifier($identifier);

        $smsData->addPhone($phone1);
        $user->setSmsData($smsData);
    }

    /**
     * Generates consistent Symfony database from Cyclos data 
     *
     * @param string $login : username of admin user 
     * @param string $password : password of admin user
     */
    public function generateDatabaseFromCyclos($login, $password)
    {
        $securityService = $this->container->get('cairn_user.security');
        $accessPlatformService = $this->container->get('cairn_user.access_platform');

        //same username than the one provided at installation
        $adminUsername = $login;
        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $users = $userRepo->myFindByRole(array('ROLE_PRO'));

        if($users){
            return 'The database is not empty ! It can\'t be generated';
        }

        $credentials = array('username'=>$adminUsername,'password'=>$password);
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

        // ************************* generate doctrine users **************************************
        $prosGroupName = $this->container->getParameter('cyclos_group_pros');
        $personsGroupName = $this->container->getParameter('cyclos_group_persons');

        $adminsGroupName = $this->container->getParameter('cyclos_group_network_admins');

        try{
            $prosGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($prosGroupName ,'MEMBER_GROUP');
            $personsGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($personsGroupName ,'MEMBER_GROUP');
            $adminsGroup = $this->container->get('cairn_user_cyclos_group_info')->getGroupVO($adminsGroupName ,'ADMIN_GROUP');

            $cyclosPros = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($prosGroup->id,array('ACTIVE','DISABLED'));
            $cyclosPersons = $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($personsGroup->id,array('ACTIVE','DISABLED'));
            $cyclosAdmins =  $this->container->get('cairn_user_cyclos_user_info')->getListInGroup($adminsGroup->id,array('ACTIVE','DISABLED'));


            $cyclosMembers = array_merge($cyclosPros, $cyclosPersons,$cyclosAdmins);

        }catch(Cyclos\ServiceException $e){
            if($e->errorCode == 'LOGIN'){
                return 'Wrong username or password provided';
            }else{
                throw $e;
            }
        }   

        $admin = $userRepo->findOneByUsername('admin_network');

        //basic user creation : create entity using data from Cyclos + add a card for all users
        echo 'INFO: ------- Creation of users based on Cyclos data' ."--------- \n";

        $rank = 0;
        foreach($cyclosMembers as $cyclosUser){
            $rank++;
            $this->createUser($cyclosUser,$admin,$rank);
        }

        $this->em->flush();
        echo 'INFO: OK !' . "\n";

        // ************************* creation of non-associated cards *******************************
        // there is a max possible number of cards to print. We let 5 possible cards to print

        $maxCards = $this->container->getParameter('max_printable_cards');
        $nbPrintedCards = $maxCards - 5;
        echo 'INFO: -------' . $nbPrintedCards . ' cards to create. Max number of printable cards : '.$maxCards . "--------- \n";

        for($i=0; $i < $nbPrintedCards; $i++){
            $card = new Card($this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),'aaaa','SINGLE'.$i,$this->container->getParameter('card_association_delay'));
            $fields = $card->generateCard($this->container->getParameter('kernel.environment'));

            $this->em->persist($card);
        }
        echo 'INFO: OK !' . "\n";


        // ************************* payments creation & synchronization ******************************************
        // foreach user living in Grenoble, credit account
        echo "INFO: ------- Credit user accounts, for each Grenoble living adherent --------- \n";

        $bankingService = $this->container->get('cairn_user_cyclos_banking_info');

        $list = $this->container->get('cairn_user_cyclos_accounttype_info')->getListAccountTypes($this->container->getParameter('cyclos_currency_cairn'),'USER');

        foreach($list as $accountType){
            if($accountType->internalName == 'compte_d_adherent'){
                $adherentAccountTypeVO = $accountType;
            }
        }
        
        //instances of ScheduledPaymentInstallmentEntryVO (these are actually installments, not transfers yet)
        //the id used to execute an operation on this installment is from an instance of ScheduledPaymentEntryVO
        //in init_data_test.py script, future transactions are made by labonnepioche
        $user = $userRepo->findOneByUsername('labonnepioche');

        $credentials = array('username'=>'labonnepioche','password'=>$password);
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);
        $futureInstallments = $bankingService->getInstallments($user->getCyclosID(),$adherentAccountTypeVO->id,array('BLOCKED','SCHEDULED'),'virement');

        $credentials = array('username'=>'admin_network','password'=>$password);
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

        //echo "INFO: ------- Get back the ".count($futureInstallments)." scheduled payments ordered by La Bonne Pioche to Alter Mag from Cyclos and synchronize--------- \n";
        //foreach($futureInstallments as $installment){
        //    $this->createOperation($installment,Operation::TYPE_TRANSACTION_SCHEDULED);
        //}

        $accountManager = $this->container->get('cairn_user.account_manager');
        
        $ub = $userRepo->createQueryBuilder('u');
         $ub->join('u.address','addr')
             ->join('addr.zipCity','zp')
             ->where('zp.city = :city')
             ->andWhere('u.username <> :username')
             ->setParameter('city','Grenoble')
             ->setParameter('username','trankilou');
        $userRepo->whereRoles($ub, array('ROLE_PRO','ROLE_PERSON'));

        $users = $ub->getQuery()->getResult();

        //here, anonymous user connects to Cyclos in order to credit user accounts without hand-made interaction
        foreach($users as $user){
            echo 'INFO: Crédit de compte de 2000 pour '.$user->getName(). "\n";
            $operation = $accountManager->creditUserAccount($user,2000, Operation::TYPE_DEPOSIT, 'Dépôt Cairn');
            $this->em->persist($operation);
            echo 'INFO: Crédit de compte de 2000 pour '.$user->getName().'... Terminé'. "\n";
        }

        echo 'INFO: OK !' . "\n";

        //********************** Fine-tune user data in order to have a diversified database ************************

        //admin has a an associated card and has already login once (avoids the compulsary redirection to first login change password)
        $credentials = array('username'=>'admin_network','password'=>$password);
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),'login',$credentials);

        $admin->setFirstLogin(false);

        $cyclosClient = $this->getClientToken($admin,'main');
        $admin->setCyclosToken($cyclosClient);

        $uniqueCode = $securityService->findAvailableCode();
        $card = new Card($this->container->getParameter('cairn_card_rows'),$this->container->getParameter('cairn_card_cols'),'aaaa',$uniqueCode,$this->container->getParameter('card_association_delay'));
        $card->addUser($admin);
        $fields = $card->generateCard($this->container->getParameter('kernel.environment'));

        //encode admin's card
        $securityService->encodeCard($card,$admin);
        $admin->setCard($card);

        echo 'INFO: ------ Set up custom properties for some users ------- ' . "\n";
        //nico_faus_perso && nico_faus_prod have same card
        $pro = $userRepo->findOneByUsername('nico_faus_prod'); 
        $proCard = $pro->getCard();

        $person = $userRepo->findOneByUsername('nico_faus_perso'); 
        $personCard = $person->getCard();

        $person->setCard($proCard);
        $personCard->getUsers()->clear();
        $this->em->remove($personCard);

        //admin is not referent of vie_integrative
        $user = $userRepo->findOneByUsername('vie_integrative'); 
        $user->getCard()->setCode('PRO_CODE');
        echo 'INFO: '.$user->getName(). ' has no referent & card code is PRO_CODE'."\n";
        $user->removeReferent($admin);
        echo 'INFO: OK !'."\n";

        $user = $userRepo->findOneByUsername('benoit_perso'); 
        $user->getCard()->setCode('PERSO_CODE');
        echo 'INFO: '.$user->getName(). ' has card code PERSON_CODE'."\n";
        echo 'INFO: OK !'."\n";

        //episol has NO card
        $user = $userRepo->findOneByUsername('episol'); 
        echo 'INFO: Pro '.$user->getName(). ' has no associated card'."\n";
        $card  = $user->getCard();
        $card->removeUser($user);                                  
        echo 'INFO: OK !'."\n";

        //speedy_andrew has NO card
        $user = $userRepo->findOneByUsername('speedy_andrew'); 
        echo 'INFO: Person '.$user->getName(). ' has no associated card'."\n";
        $card  = $user->getCard();
        $card->removeUser($user);                                  
        echo 'INFO: OK !'."\n";

        //NaturaVie has NO card and admin not referent and never logged in
        $user = $userRepo->findOneByUsername('NaturaVie'); 
        echo 'INFO: '.$user->getName(). ' has no associated card and no referent'."\n";
        $user->removeReferent($admin);
        $user->setLastLogin(NULL);
        $card  = $user->getCard();
        $card->removeUser($user);                                  
        echo 'INFO: OK !'."\n";

        //nico_faus_prod has beneficiary labonnepioche
        $debitor = $userRepo->findOneByUsername('nico_faus_prod'); 
        $creditor = $userRepo->findOneByUsername('labonnepioche'); 
        echo 'INFO: '.$creditor->getName(). ' is beneficiary of '.$debitor->getName()."\n";

        $benef = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($creditor->getCyclosID());
        $ICC = $benef->number;
        $beneficiary = new Beneficiary();
        $beneficiary->setICC($ICC);
        $beneficiary->setUser($creditor);
        $debitor->addBeneficiary($beneficiary);
        $beneficiary->addSource($debitor);
        echo 'INFO: OK !'."\n";

        //le_marque_page has beneficiary labonnepioche
        $debitor = $userRepo->findOneByUsername('le_marque_page'); 
        echo 'INFO: '.$creditor->getName(). ' is beneficiary of '.$debitor->getName()."\n";
        $debitor->addBeneficiary($beneficiary);
        $beneficiary->addSource($debitor);
        echo 'INFO: OK !'."\n";

        //pain_beauvoir has beneficiary ferme_bressot
        $debitor = $userRepo->findOneByUsername('pain_beauvoir'); 
        $creditor = $userRepo->findOneByUsername('ferme_bressot'); 
        echo 'INFO: '.$creditor->getName(). ' is beneficiary of '.$debitor->getName()."\n";

        $benef = $this->container->get('cairn_user_cyclos_account_info')->getDefaultAccount($creditor->getCyclosID());
        $ICC = $benef->number;
        $beneficiary = new Beneficiary();
        $beneficiary->setICC($ICC);
        $beneficiary->setUser($creditor);

        $debitor->addBeneficiary($beneficiary);
        $beneficiary->addSource($debitor);
        echo 'INFO: OK !'."\n";

        //user has requested a removal and has null account balance on Cyclos-side
        $user = $userRepo->findOneByUsername('Biocoop'); 
        $user->setEnabled(false);
        $user->setRemovalRequest(true);

        //user has requested a removal and has non-null account balance on Cyclos-side
        $user = $userRepo->findOneByUsername('Alpes_EcoTour'); 
        echo 'INFO: '.$user->getName(). ' has requested to be removed'."\n";
        $user->setEnabled(false);
        $user->setRemovalRequest(true);
        echo 'INFO: OK !'."\n";

        //user is blocked but has already been able to log in
        $user = $userRepo->findOneByUsername('tout_1_fromage'); 
        echo 'INFO: '.$user->getName(). ' is blocked but has already logged in'."\n";

        $user->setEnabled(false);

        echo 'INFO: OK !'."\n";

        //users have ROLE_ADMIN as referent
        $user1 = $userRepo->findOneByUsername('episol'); 
        $user2 = $userRepo->findOneByUsername('lib_colibri'); 

        $admin1 = $userRepo->findOneByUsername('gl_grenoble'); 
        $admin2  = $userRepo->findOneByUsername('gl_voiron'); 

        echo 'INFO: '.$admin1->getName(). ' becomes referent of '. $user1->getName()."\n";
        $user1->addReferent($admin1);
        echo 'INFO: '.$admin2->getName(). ' becomes referent of '. $user2->getName()."\n";
        $user2->addReferent($admin2);
        echo 'INFO: OK !'."\n";

        //setup phone number and sms information for pros and persons

        //all users in this array will have an access client on Cyclos side
        $usersWithSmsInfo = array();

        $pro1 = $userRepo->findOneByUsername('nico_faus_prod'); 
        $this->createSmsData($pro1, '+33612345678', 'NICOPROD');
        $pro1->getPhones()[0]->setPaymentEnabled(true);


        $person1 = $userRepo->findOneByUsername('nico_faus_perso'); 
        $this->createSmsData($person1, '+33612345678');

        echo 'INFO: ' .$pro1->getName(). ' with role PRO, has phone number : '. $pro1->getPhoneNumbers()[0]."\n";
        echo 'INFO: ' .$pro1->getName(). ' with role PRO has ENabled all sms operations : '."\n";
        echo 'INFO: ' .$person1->getName(). ' with role PERSON, has same phone number, personally and for '. $pro1->getName()."\n";
        echo 'INFO: ' .$person1->getName(). ' with role PERSON has ENabled all sms operations : '."\n";
        $usersWithSmsInfo[] = $person1;
        $usersWithSmsInfo[] = $pro1;

        $pro2 = $userRepo->findOneByUsername('maltobar'); 
        $this->createSmsData($pro2,'+33611223344','MALTOBAR' );
        $pro2->getPhones()[0]->setPaymentEnabled(true);

        $person2 = $userRepo->findOneByUsername('benoit_perso'); 
        $this->createSmsData($person2,'+33644332211');
        $person2->getPhones()[0]->setPaymentEnabled(false);

        echo 'INFO: ' .$pro2->getName(). ' with role PRO, has phone number : '. $pro2->getPhoneNumbers()[0]."\n";
        echo 'INFO: ' .$pro2->getName(). ' with role PRO has ENabled all sms operations : '."\n";
        echo 'INFO: ' .$person2->getName(). ' with role PERSON, has phone number : '. $person2->getPhoneNumbers()[0]."\n";
        echo 'INFO: ' .$person2->getName(). ' with role PERSON has DISabled sms operations'."\n";
        echo 'INFO: OK !'."\n";
        $usersWithSmsInfo[] = $person2;
        $usersWithSmsInfo[] = $pro2;

        $pro = $userRepo->findOneByUsername('epicerie_sol'); 

        echo 'INFO: '. $pro->getName(). 'has DISabled sms payments but can receive payments at 0655667788 '."\n";
        $this->createSmsData($pro,'+33655667788','AMANSOL');
        $usersWithSmsInfo[] = $pro;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has requested three times a new phone number without validation'."\n";
        $user = $userRepo->findOneByUsername('crabe_arnold'); 
        $this->createSmsData($user,'+33711111111','CRABEARNOLD');
        $user->setNbPhoneNumberRequests(3);
        $usersWithSmsInfo[] = $user;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has a phone number and admin is not referent'."\n";
        $user = $userRepo->findOneByUsername('stuart_andrew'); 
        $user->removeReferent($admin);
        $this->createSmsData($user,'+33743434343','STUART');
        $usersWithSmsInfo[] = $user;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has one last trial to validate his phone number'."\n";
        $user = $userRepo->findOneByUsername('hirundo_archi'); 
        $this->createSmsData($user,'+33722222222','HIRUNDO');

        $user->getPhones()[0]->setPaymentEnabled(true);
        $user->setNbPhoneNumberRequests(1);
        $user->setPhoneNumberActivationTries(2);
        $usersWithSmsInfo[] = $user;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has several remaining tries to validate his phone number and has disabled sms ops'."\n";
        $user = $userRepo->findOneByUsername('DrDBrew'); 
        $this->createSmsData($user,'+33733333333','DRDBREW');

        $user->setNbPhoneNumberRequests(1);
        $user->setPhoneNumberActivationTries(0);
        $usersWithSmsInfo[] = $user;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has phone number and is blocked'."\n";
        $user = $userRepo->findOneByUsername('la_mandragore'); 
        $user->setEnabled(false);
        $this->createSmsData($user,'+33744444444','MANDRAGORE');

        $user->setNbPhoneNumberRequests(1);
        $user->setPhoneNumberActivationTries(0);
        $usersWithSmsInfo[] = $user;
        echo 'INFO: OK !'."\n";

        echo 'INFO: '. $user->getName(). ' has an unexisting access client'."\n";
        $user = $userRepo->findOneByUsername('comblant_michel'); 
        $this->createSmsData($user,'+33788888888');

        $user->getSmsData()->setSmsClient('dlncnlcdlkkncsjdj');

        $user->setNbPhoneNumberRequests(1);
        $user->setPhoneNumberActivationTries(0);

        echo 'INFO: ------ Set up Cyclos sms access clients for users with phone number ------- ' . "\n";
        foreach($usersWithSmsInfo as $user){
            $this->setUpSmsAccessClient($user, $this->em);
        }


        //Forced to set user status after creation of users, access clients... Otherwise, user can't access Cyclos and do any operation
        echo 'INFO: ------ Set up Cyclos user status ------- ' . "\n";
        $user = $userRepo->findOneByUsername('la_mandragore'); 
        echo 'INFO: '. $user->getName(). ' has status DISABLED on Cyclos side'."\n";

        $accessPlatformService->changeUserStatus($user, 'DISABLED');
        echo 'INFO: OK !'."\n";

        $user = $userRepo->findOneByUsername('tout_1_fromage'); 
        echo 'INFO: '. $user->getName(). ' has status DISABLED on Cyclos side'."\n";

        $accessPlatformService->changeUserStatus($user, 'DISABLED');
        echo 'INFO: OK !'."\n";

        $user = $userRepo->findOneByUsername('Biocoop'); 
        echo 'INFO: '. $user->getName(). ' has status DISABLED on Cyclos side'."\n";

        $accessPlatformService->changeUserStatus($user, 'DISABLED');
        echo 'INFO: OK !'."\n";

        $user = $userRepo->findOneByUsername('Alpes_EcoTour'); 
        echo 'INFO: '. $user->getName(). ' has status DISABLED on Cyclos side'."\n";

        $accessPlatformService->changeUserStatus($user, 'DISABLED');
        echo 'INFO: OK !'."\n";


        //generate mandates for users as persons
        echo 'INFO: ------ Set up mandates ------- ' . "\n";

        
        $contractor = $userRepo->findOneByUsername('lacreuse_desiderata'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::COMPLETE,'-7 months','-6 months','-1 month');
        $this->em->persist($mandate);
       
        $contractor = $userRepo->findOneByUsername('barbare_cohen'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::CANCELED,'-4 months','-3 months','+3 months');
        $this->em->persist($mandate);

        $contractor = $userRepo->findOneByUsername('crabe_arnold'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::UP_TO_DATE,'-4 months','-3 months','+3 months');
        $this->em->persist($mandate);

        $contractor = $userRepo->findOneByUsername('tous_andre'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::OVERDUE,'-4 months','-3 months','+3 months');
        $this->em->persist($mandate);

        $contractor = $userRepo->findOneByUsername('gjanssens'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::SCHEDULED,'-1 month','+1 month','+7 months');
        $this->em->persist($mandate);


        //need a pro with a mandate and a null account
        $contractor = $userRepo->findOneByUsername('montagne_arts'); 
        $mandate = $this->createMandate($contractor, 20, Mandate::SCHEDULED,'-1 day','+1 month','+7 months');
        $this->em->persist($mandate);

        //generate an helloasso payment
        echo 'INFO: ------ Set up helloasso payments ------- ' . "\n";
        $creditor = $userRepo->findOneByUsername('mazmax'); 

        $helloasso = new HelloassoConversion();

        $helloasso->setPaymentID('000040877783');
        $helloasso->setDate(new \Datetime());
        $helloasso->setAmount(40);
        $helloasso->setEmail($creditor->getEmail());
        $helloasso->setCreditorName($creditor->getName());

        $this->em->persist($helloasso);

        //generate account score configurations
        echo 'INFO: ------ Set up account score configurations ------- ' . "\n";

        $contractor = $userRepo->findOneByUsername('episol'); 

        echo 'INFO : '.$contractor->getName().' has an account score config with csv format' ."\n";
        $accountScore = new AccountScore();
        $accountScore->setUser($contractor);
        $accountScore->setFormat('csv');

        $this->em->persist($accountScore);
        echo 'INFO: OK !'."\n";


        $contractor = $userRepo->findOneByUsername('tout_1_fromage'); 

        echo 'INFO : '.$contractor->getName().' has an account score config with confirmation token' ."\n";
        $accountScore = new AccountScore();
        $accountScore->setUser($contractor);
        $accountScore->setEmail('test_fromage@test.fr');
        $accountScore->setConfirmationToken($securityService->generateUrlToken());

        $this->em->persist($accountScore);
        echo 'INFO: OK !'."\n";


        $this->em->flush();
        echo 'INFO: OK !'."\n";

        return 'Database successfully generated !';

    }

    private function createMandate(User $contractor, $amount,$status,$createdAt,$beforeToday,$afterToday)
    {
        $accountManager = $this->container->get('cairn_user.account_manager');
        
        $mandate = new Mandate();
        $mandate->setStatus($status);

        echo 'INFO: '. $contractor->getName(). ' has a mandate '.$mandate->getStatusName($status)."\n";

        $mandate->setContractor($contractor);
        $mandate->setAmount($amount);

        //create fake mandate document doc
        $absoluteWebDir = $this->container->getParameter('kernel.project_dir').'/web/';
        $originalName = 'poster_sms.pdf';
        $absolutePath = $absoluteWebDir.$originalName;

        $file = new UploadedFile($absolutePath,$originalName,null,null,null, true);

        $document = new File();
        $document->setUrl($file->guessExtension());
        $document->setAlt($file->getClientOriginalName());

        $mandate->addMandateDocument($document);
        $document->setMandate($mandate);

        $today = new \Datetime();

        $clone = clone $today;
        $before = new \Datetime(date_modify($clone, $beforeToday)->format('d-m-Y'));

        if($before->format('d') >= 25){
            $before->modify('first day of next month');
        }

        $clone = clone $today;
        $after = new \Datetime(date_modify($clone,$afterToday)->format('d-m-Y'));

        if($after->format('d') >= 25){
            $after->modify('first day of next month');
        }

        $mandate->setBeginAt($before);
        $mandate->setEndAt($after);
        $mandate->setCreatedAt(date_modify($clone,$createdAt));
        $mandate->setStatus($status);

        $count = $accountManager->getConsistentOperationsCount($mandate,$today);

        if($status == Mandate::OVERDUE){ $count -= 1; }

        for($i = 0; $i < $count; $i++){
            $operation = $accountManager->creditUserAccount($contractor, $amount, Operation::TYPE_MANDATE, 'Règlement de mandat');

            $mandate->addOperation($operation);

            $this->em->persist($operation);
            $operation->setMandate($mandate);
        }

        echo 'INFO: OK !'."\n";

        return $mandate;

    }
}

<?php                                                                          
// src/Cairn/UserBundle/Service/Security.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserBundle\Repository\OperationRepository;
use Cairn\UserBundle\Repository\CardRepository;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Entity\Phone;

use Cairn\UserCyclosBundle\Entity\UserIdentificationManager;
use Cairn\UserCyclosBundle\Service\UserIdentificationInfo;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\BCryptPasswordEncoder;

/**
 * This class contains services related to security
 *
 */
class Security
{
    /**
     *@var UserRepository $userRepo
     */
    protected $userRepo;

    /**
     *@var OperationRepository $operationRepo
     */
    protected $operationRepo;

    /**
     *@var CardRepository $cardRepo
     */
    protected $cardRepo;

    protected $tokenStorage;

    protected $encoderFactory;

    protected $userIdentificationInfo;

    protected $secret;

    protected $smsDailyThresholds;

    protected $mobileDailyThresholds;

    public function __construct(UserRepository $userRepo,OperationRepository $operationRepo, CardRepository $cardRepo, TokenStorageInterface $tokenStorage, EncoderFactory $encoderFactory,UserIdentificationInfo $userIdentificationInfo,string $secret, array $smsDailyThresholds, array $mobileDailyThresholds)
    {
        $this->userRepo = $userRepo;
        $this->operationRepo = $operationRepo;
        $this->cardRepo = $cardRepo;
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
        $this->userIdentificationInfo= $userIdentificationInfo;
        $this->secret = $secret;
        $this->smsDailyThresholds = $smsDailyThresholds; 
        $this->mobileDailyThresholds = $mobileDailyThresholds; 
    }

    /**
     * Returns current Symfony user using token storage
     *
     * @return User current user
     */
    public function getCurrentUser()
    {
        $token = $this->tokenStorage->getToken();
        if($token){                                                            
            $currentUser = $token->getUser();                                  
            if($currentUser instanceof \Cairn\UserBundle\Entity\User){
                return $currentUser;
            }
        }
        return NULL;
    }

    /**
     * Returns true if current request is considered as sensible, false otherwise
     *
     * For most of the urls matcheable by our application, their corresponding route is enough to identify the request as sensible or not.
     * But for a few of them, the values of request parameters must be investigated. If the url is sensible, the security card is asked
     * to the user to go further.
     *
     * Exemple : Revoking a security card can be done either by an admin for an user or by an adherent for himself. Revoking a card is 
     * necessary if the user lost it. Therefore, an adherent who wants to declare a revocation of his card must be able to do it without
     * any validation of his identity by security card (because he lost it...)
     * For this case, the request parameter "username" allows to know who is doing the revocation request, and who will have his card
     * revoked.
     *
     *@param string $route route name of the current request
     *@param array $parameters request parameters
     *@return boolean
     */
    public function isSensibleOperation($route, $parameters)
    {
        $currentUser = $this->getCurrentUser();
        $sensibleRoutes = SecurityEvents::SENSIBLE_ROUTES;                     
        $sensibleUrls = SecurityEvents::SENSIBLE_URLS;                         

        $isSensibleUrl = self::isSensibleURL($route,$parameters);    

        $isSensibleRoute = in_array($route,$sensibleRoutes);                   

        if($route == 'cairn_user_card_revoke' || $route == 'cairn_user_card_associate'){
            if(! ($parameters['username'] == $currentUser->getUsername() )){//|| $parameters['username'] == $currentUser->getUsername()) ){
                $isSensibleUrl = true;
            }
        }elseif(strpos($route,'cairn_user_cyclos') !== false){
            $isSensibleRoute = true;
        }

        return ($isSensibleUrl || $isSensibleRoute);  
    }

    /**                                                                        
     *Returns true if the URL matches a sensible operation in SENSIBLE_URLS, false otherwise
     *                                                                           
     *This function first finds if the operation corresponds to a sensible route in SENSIBLE_URLS, then analyzes the different route 
     *parameters provided, and returns true if at least one route parameter belongs to the list of decisive parameters defining a 
     *sensible operation.                                                      
     */                                                                        
    static function isSensibleURL($route, $parameters)                         
    {                                                                          
        $sensibleUrls = SecurityEvents::SENSIBLE_URLS;                                   

        $cardinal = count($sensibleUrls);                                      

        $cmpt = 0;                                                             
        while($cmpt < $cardinal){                                              
            if($route == $sensibleUrls[$cmpt][0]){                             
                break;                                                         
            }                                                                  
            else{                                                              
                $cmpt = $cmpt + 1;                                             
            }                                                                  
        }                                                                      

        if($cmpt != $cardinal){//if a route matches, check parameters          
            return (count(array_intersect_assoc($sensibleUrls[$cmpt][1], $parameters)) >0) ;
        }                                                                      

        return false;                                                          
    }                

    public function assignDefaultReferents($user)
    {
        $superAdmins = $this->userRepo->myFindByRole(array('ROLE_SUPER_ADMIN'));

        //we set referent roles
        foreach($superAdmins as $superAdmin){
            $user->addReferent($superAdmin);
        } 

        //if user is a person, any local group is referent
        if($user->hasRole('ROLE_PERSON')){
            $admins = $this->userRepo->myFindByRole(array('ROLE_ADMIN'));
            foreach($admins as $admin){
                $user->addReferent($admin);
            } 
        } 

        //if user is a local group, he is referent of any individual adherent
        if($user->hasRole('ROLE_ADMIN')){
            $persons = $this->userRepo->myFindByRole(array('ROLE_PERSON'));
            foreach($persons as $person){
                $person->addReferent($user);
            } 
        } 

        //automatically assigns a local group as referent to a pro if they have same city
        if($user->hasRole('ROLE_PRO')){
            $localGroup = $this->userRepo->findAdminWithCity($user->getCity());
            if($localGroup){
                if(!$user->hasReferent($localGroup)){//case of registration by admin where assignation is done in the registration form
                    $user->addReferent($localGroup);
                }
            }
        }
    }


    private function getKey($length){
        $key = $this->secret;
        if (strlen($key) >= $length){
            return substr($key,0,$length);
        }else{
            return str_pad('', $length, $key);
        }
    }


    //Chiffre_de_Vigenère
    public function vigenereEncode($string){
        $return = str_pad('', strlen($string), ' ', STR_PAD_LEFT);
        $key = $this->getKey(strlen($string));
        for ( $pos=0; $pos < strlen($string); $pos ++ ) {
            $return[$pos] = chr((ord($string[$pos]) + ord($key[$pos])) % 256);
        }
        return base64_encode($return);
    }


    public function vigenereDecode($string){
        $string = base64_decode($string);
        $return = str_pad('', strlen($string), ' ', STR_PAD_LEFT);
        $key = $this->getKey(strlen($string));
        for ( $pos=0; $pos < strlen($string); $pos ++ ) {
            $return[$pos] = chr((ord($string[$pos]) - ord($key[$pos])) % 256);
        }
        return $return;
    }

    /**
     * No character O or 0 in the encoded Code in order not to confuse user
     *
     */
    public function findAvailableCode()                                        
    {                                                                          
        $uniqueCode = substr($this->generateToken(),0,5);        
        $existingCard = $this->cardRepo->findAvailableCardWithCode($uniqueCode);         

        $encodedCode = $this->vigenereEncode($uniqueCode);

        while( (strpos($encodedCode,'O') !==  false) || (strpos($encodedCode,'0') !== false) || $existingCard ){
            $uniqueCode = substr($this->generateToken(),0,5);    
            $existingCard = $this->cardRepo->findAvailableCardWithCode($uniqueCode);     

            $encodedCode = $this->vigenereEncode($uniqueCode);

        }                                                                      

        return $uniqueCode;                                                    
    }


    public function generateToken()
    {
        $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
        return $salt;
    }

    public function generateUrlToken()
    {
        return rtrim(strtr(base64_encode(random_bytes(32)), '+/', '-_'), '=');
    }

    public function encodeCard(Card $card, User $user)
    {
        //same encoder for all users
        $encoder = $this->encoderFactory->getEncoder($user);  

        $fields = $card->getFields();
        $nbRows = $card->getRows();                                            
        $nbCols = $card->getCols();                                            

        for($row = 0; $row < $nbRows; $row++){                                 
            for($col = 0; $col < $nbCols; $col++){                             
                $encoded_field = $encoder->encodePassword($fields[$row][$col],$card->getSalt());
                $fields[$row][$col] = substr($encoded_field,0,4);              
            }                                                                  
        }  

        $card->setFields($fields);
    }


    /**
     *
     */
    public function createAccessClient(User $user, $type)
    {
        $userIdentificationManager = new UserIdentificationManager();
        $userIdentificationManager->createAccessClient($user->getCyclosID(),$type);
    }

    public function changeAccessClientStatus($accessClientVO,$status)
    {
        $userIdentificationManager = new UserIdentificationManager();

        if($status == 'ACTIVE'){
            return $userIdentificationManager->activateAccessClient($accessClientVO);
        }
        return $userIdentificationManager->changeAccessClientStatus($accessClientVO,$status);
    }


    public function getSmsClient(User $user)
    {
        if($user->getSmsData()->getSmsClient()){
            return $this->vigenereDecode($user->getSmsData()->getSmsClient()) ;
        }
        return NULL;
    }

    
    /**
     * Returns true if the SMS/Mobile APP payment is suspicious, false otherwise
     *
     * A payment is considered as suspicious if the payment amount is greater than a custom limit, or if the number of payments executed 
     * the same day by the same person reaches a custom limit
     *
     * @param Operation $operation  Payment by SMS or Mobile app
     * @return array
     */
    public function paymentValidationState(Operation $operation)
    {
        $res = ['validation'=>false,'suspicious'=>false];
        if($operation->isSmsPayment()){
            $thresholds = $this->smsDailyThresholds;
        }elseif($operation->getType() == Operation::TYPE_MOBILE_APP){
            $thresholds = $this->mobileDailyThresholds;
        }else{
            return $res;
        }

        $debitor = $operation->getDebitor();

        $ob = $this->operationRepo->createQueryBuilder('o');
        $this->operationRepo
            ->whereType($ob, $operation->getType())
            ->whereDebitor($ob,$debitor)
            ->whereCurrentDay($ob);

        $operations = $ob->getQuery()->getResult();
        $nbOperations = count($operations) + 1;

        $totalDayAmount = $this->operationRepo->countTotalAmount($ob);

        $totalDayAmount = (!$totalDayAmount) ? 0 : $totalDayAmount;
        $totalDayAmount += $operation->getAmount();


        //FIRST, CHECK THE BLOCK STATEMENTS
        if( $operation->getAmount() >= $thresholds['amount']['block'] ){
            $res['suspicious'] = true;
            return $res;
        }
        if($totalDayAmount >= $thresholds['amount']['block']){
            $res['suspicious'] = true;
            return $res;
        }
        if($nbOperations >= $thresholds['qty']['block']){ 
            $res['suspicious'] = true;
            return $res;
        }

        //THEN, CHECK THE VALIDATION STATEMENTS
        if($operation->getAmount() >= $thresholds['amount']['unique'] ){
            $res['validation'] = true;
            return $res;
        }
        if($totalDayAmount >= $thresholds['amount']['cumulated']){
            $res['validation'] = true;
            return $res;
        }
        if($nbOperations >= $thresholds['qty']['step']){
            $res['validation'] = true;
            return $res;
        }

        return $res;
    }


    public function parseAuthorizationHeader(string $authorizationHeader)
    {
        preg_match('#^HMAC\-(\w+)\s*(Bearer\s*(\w+))?\s*(Signature=(\d+)\:(\w+))$#',$authorizationHeader, $matches_authorization);

        if(! $matches_authorization){
            return NULL;
        }
        return [
            'algo'=> strToLower($matches_authorization[1]),
            'timestamp' => $matches_authorization[5],
            'credential' => $matches_authorization[3],
            'signature' => $matches_authorization[6]
        ];
    }

}

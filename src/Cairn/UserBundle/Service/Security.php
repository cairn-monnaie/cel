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

    public function __construct(UserRepository $userRepo,OperationRepository $operationRepo, CardRepository $cardRepo, TokenStorageInterface $tokenStorage, EncoderFactory $encoderFactory,UserIdentificationInfo $userIdentificationInfo, $secret)
    {
        $this->userRepo = $userRepo;
        $this->operationRepo = $operationRepo;
        $this->cardRepo = $cardRepo;
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
        $this->userIdentificationInfo= $userIdentificationInfo;
        $this->secret = $secret;
    }

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

    public function isSensibleOperation($route, $parameters)
    {
        $currentUser = $this->getCurrentUser();
        $sensibleRoutes = SecurityEvents::SENSIBLE_ROUTES;                     
        $sensibleUrls = SecurityEvents::SENSIBLE_URLS;                         

        $isSensibleUrl = self::isSensibleURL($route,$parameters);    

        $isSensibleRoute = in_array($route,$sensibleRoutes);                   

        if($route == 'cairn_user_card_revoke' || $route == 'cairn_user_card_associate'){
            if(! ($this->userRepo->findOneBy(array('id'=>$parameters['id'])) === $currentUser)){
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


    public function findAvailableCode()                                        
    {                                                                          
        $uniqueCode = substr($this->generateCardSalt(),0,5);        
        $existingCard = $this->cardRepo->findAvailableCardWithCode($uniqueCode);         

        while($existingCard){                                                  
            $uniqueCode = substr($this->generateCardSalt(),0,5);    
            $existingCard = $this->cardRepo->findAvailableCardWithCode($uniqueCode);     
        }                                                                      

        return $uniqueCode;                                                    
    }


    public function generateCardSalt()
    {
        $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
        return $salt;
    }


    public function encodeCard(Card $card)
    {
        $encoder = $this->encoderFactory->getEncoder($card->getUser());  

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
        return str_replace($this->secret, '', $this->vigenereDecode($user->getSmsClient()) );
    }


    /**
     *Beware, input Operation is not persisted, and is relevant only for sms payments
     *
     */
    public function paymentNeedsValidation(Operation $operation)
    {
        if(! $operation->isSmsPayment()){ return false; }

        //criteria 1 : second payment to the same pro in the same day
        $ob = $this->operationRepo->createQueryBuilder('o');
        $this->operationRepo
            ->whereType($ob, Operation::TYPE_SMS_PAYMENT)
            ->whereDebitor($ob,$operation->getDebitor())
            ->whereCreditor($ob,$operation->getCreditor())
            ->whereCurrentDay($ob);

        $operations = $ob->getQuery()->getResult();

        if(count($operations) > 0){ return true; }

        //criteria 2 : threshold of amount spent in one day
        $ob = $this->operationRepo->createQueryBuilder('o');
        $this->operationRepo
            ->whereType($ob, Operation::TYPE_SMS_PAYMENT)
            ->whereDebitor($ob,$operation->getDebitor())
            ->whereCurrentDay($ob);

        $totalDayAmount = $this->operationRepo->countTotalAmount($ob);
        if($totalDayAmount > $operation->getDebitor()->getAmountDailyThreshold()){
           return true; 
        }
        
        //criteria 3 : number of current day payments (lower than threshold ?)
        $ob = $this->operationRepo->createQueryBuilder('o');
        $this->operationRepo
            ->whereType($ob, Operation::TYPE_SMS_PAYMENT)
            ->whereDebitor($ob,$operation->getDebitor())
            ->whereAmountComparedWith($ob, $operation->getAmountDailyThreshold(), 'lt')
            ->whereCurrentDay($ob);

        $operations = $ob->getQuery()->getResult();
        if(count($operations) > $operation->getDebitor()->getNumberPaymentsThreshold()){ return true; }

        return false;
    }

    }
}

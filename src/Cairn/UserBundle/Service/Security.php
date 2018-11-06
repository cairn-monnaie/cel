<?php                                                                          
// src/Cairn/UserBundle/Service/Security.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Repository\UserRepository;
use Cairn\UserBundle\Entity\User;

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

    protected $tokenStorage;

    protected $encoderFactory;

    public function __construct(UserRepository $userRepo, TokenStorageInterface $tokenStorage, EncoderFactory $encoderFactory)
    {
        $this->userRepo = $userRepo;
        $this->tokenStorage = $tokenStorage;
        $this->encoderFactory = $encoderFactory;
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

        if($route == 'cairn_user_card_revoke' || $route == 'cairn_user_card_order'){
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

    public function generateCardSalt(User $user)
    {
        $encoder = $this->encoderFactory->getEncoder($user);

        if ($encoder instanceof BCryptPasswordEncoder) {                   
            $salt = NULL;                                                  
        } else {                                                           
            $salt = rtrim(str_replace('+', '.', base64_encode(random_bytes(32))), '=');
        }                    
        return $salt;
    }
}

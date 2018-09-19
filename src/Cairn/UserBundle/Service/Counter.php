<?php                                                                          
// src/Cairn/UserBundle/Service/Counter.php                             

namespace Cairn\UserBundle\Service;                                      

class Counter
{

    public function reinitializeTries($user,$type)
    {
        if($type == 'password'){
            $user->setPasswordTries(0);
        }
        elseif($type == 'cardKey'){
            $user->setCardKeyTries(0);
        }
    }

    public function incrementTries($user,$type)
    {
        if($type == 'password'){
            $user->setPasswordTries($user->getPasswordTries() + 1);
        }
        elseif($type == 'cardKey'){
            $user->setCardKeyTries($user->getCardKeyTries() + 1);
        }
    }


}   

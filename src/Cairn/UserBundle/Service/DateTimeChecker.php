<?php
// src/Cairn/UserBundle/Service/DateTimeChecker.php

namespace Cairn\UserBundle\Service;

/**
 * This class contains services related to the date times
 *
 */
class DateTimeChecker
{

    /**
     *
     *@param DateTime
     *@param DateTime
     *@throws Symfony\Component\HttpKernel\Exception 
     *@return boolean
     */
    public function isValidInterval($begin,$end)
    {
        if( (!$begin && !$end) || ($begin && $end)){                   
            if($begin && $end){                                        
                if($begin->diff($end)->invert == 1){                   
                    throw new \Exception('La date de début ne peut être antérieure à la date de fin.');
                }                                                      
            }
        }else{                                                         
            throw new \Exception('Vous n\'avez spécifié qu\'une seule date');
        }   

        return true;
    }
}

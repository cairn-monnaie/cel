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
        $interval = $begin->diff($end);
        if($interval->invert == 1 || ($interval->invert == 0 && $interval->days == 0)){                   
            return false;
        }                                                      

        return true;
    }
}

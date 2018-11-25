<?php
// src/Cairn/UserCyclosBundle/Service/PasswordInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to password objects
 *                                                                             
 */
class PasswordInfo
{

    /**                                                                        
     * Deals with all password management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\PasswordService $passwordService                                            
     */
    private $passwordService;

    /**                                                                        
     * Deals with all password types management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\PasswordTypeService $passwordTypeService                                            
     */
    private $passwordTypeService;

    public function __construct()
    {
        $this->passwordService = new Cyclos\PasswordService();
        $this->passwordTypeService = new Cyclos\PasswordTypeService();
    }

    /**
     * Get all password types
     *
     *@return stdClass representing Java type: java.util.List of org.cyclos.services.access.PasswordTypeVO
     */
    public function getListOfPasswordTypes()
    {
        return $this->passwordTypeService->_list();
    }

    /**
     * Get a password type by internal name
     *
     * @return stdClass representing Java type: org.cyclos.model.access.access.PasswordTypeVO
     */
    public function getPasswordTypeVO($internalName)
    {
        $list = $this->getListOfPasswordTypes();
        foreach($list as $passwordType)
        {
            if($passwordType->internalName == $internalName){
                return $passwordType;
            }
        }
        return NULL;
    }
}

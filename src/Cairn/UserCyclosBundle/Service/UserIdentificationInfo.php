<?php
// src/Cairn/UserCyclosBundle/Service/UserIdentificationInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to user identification methods
 *                                                                             
 */
class UserIdentificationInfo
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

    private $principalTypeService;

    private $tokenService;

    private $accessClientService;

    public function __construct()
    {
        $this->passwordService = new Cyclos\PasswordService();
        $this->passwordTypeService = new Cyclos\PasswordTypeService();
        $this->principalTypeService = new Cyclos\PrincipalTypeService();
        $this->tokenService = new Cyclos\TokenService();
        $this->accessClientService = new Cyclos\AccessClientService();
    }

    public function getAccessClientByToken($token, $status)
    {
        $acLocatorVO = array('token'=>$token, 'status'=>$status);
        return $this->accessClientService->locate($acLocatorVO);
    }

    public function getAccessClientByUser($userID, $type, $status = NULL)
    {
        $query = new \stdClass();
        $query->user = $userID;
        $query->status = $status;
        $query->type = $type;

        $accessClientsVO = $this->accessClientService->search($query)->pageItems;
        foreach ($accessClientsVO as $accessClientVO){
            if($accessClientVO->user->id == $userID){
                return $accessClientVO;
            }
        }
        return NULL;

    }

    public function getTokenVO($userID, $value)
    {
        $query = new \stdClass();
        $query->value = $value;
        $query->user = $userID;

        $tokensVO = $this->tokenService->search($query)->pageItems;
        foreach ($tokensVO as $tokenVO){
            if($tokenVO->user->id == $userID){
                return $tokenVO;
            }
        }
        return NULL;
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

    /**
     * Get all principal types
     *
     *@return stdClass representing Java type: java.util.List of org.cyclos.model.access.principaltypes.PrincipalTypeVO
     */
    public function getListOfPrincipalTypes()
    {
        return $this->principalTypeService->_list();
    }

    /**
     * Get a principal type by internal name
     *
     * @return stdClass representing Java type: org.cyclos.model.access.principaltypes.PrincipalTypeVO
     */
    public function getPrincipalTypeVO($internalName)
    {
        $list = $this->getListOfPrincipalTypes();
        foreach($list as $principalType)
        {
            if($principalType->internalName == $internalName){
                return $principalType;
            }
        }
        return NULL;
    }

}

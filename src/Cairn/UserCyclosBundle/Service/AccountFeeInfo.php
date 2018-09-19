<?php
// src/Cairn/UserCyclosBundle/Service/AccountFeeInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;
//use Cairn\AccountFeeCyclosBundle\configureCyclos;

class AccountFeeInfo
{

    private $accountFeeService;

    public function __construct()
    {
        $this->accountFeeService = new Cyclos\AccountFeeService();
    }

    /**
     * Provides ID of accountFee @param
     *
     * @param string $name
     * @return int 
     */
    public function getAccountFeeID($name)
    {
        $query = new \stdClass();
        $query->name = $name;
        $res = $this->accountFeeService->search($query);
        
        if(sizeof($res->pageItems) == 0){
            return NULL;
        }
        return $res->pageItems[0]->id;
    }

    public function getAccountFeeData($name)
    {
        $id = $this->getAccountFeeID($name);
        return $this->accountFeeService->getData($id);
    }

     /*
     * get account type DTO
     * @param string $name
     * @return DTO
     */
    public function getAccountFeeDTO($name)
    {
        $id = $this->getAccountFeeID($name);
        if($id == NULL){
            return $id;
        }
        return $this->accountFeeService->load($id);
    }

}

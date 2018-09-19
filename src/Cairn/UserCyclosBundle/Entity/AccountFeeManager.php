<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class AccountFeeManager
{
    private $accountFeeService;

    public function __construct()
    {
        $this->accountFeeService = new Cyclos\AccountFeeService();
    }

    public function removeAccountFee($id){
        return $this->accountFeeService->remove($id);}

    public function editAccountFee($accountFeeDTO)
    {
        return $this->accountFeeService->save($accountFeeDTO);
    }

}

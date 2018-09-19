<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class AccountTypeManager
{
    private $accountTypeService;

    public function __construct()
    {
        $this->accountTypeService = new Cyclos\AccountTypeService();
    }

    public function removeAccountType($id){
        return $this->accountTypeService->remove($id);}

    public function editAccountType($accountTypeDTO)
    {
        return $this->accountTypeService->save($accountTypeDTO);
    }

}

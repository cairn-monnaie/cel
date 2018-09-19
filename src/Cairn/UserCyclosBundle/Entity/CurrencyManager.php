<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class CurrencyManager
{
    private $currencyService;

    public function __construct()
    {
        $this->currencyService = new Cyclos\CurrencyService();
    }


    public function editCurrency($currencyDTO){
        return $this->currencyService->save($currencyDTO);
    }

    public function removeCurrency($id){
        return $this->currencyService->remove($id);}

    public function changeStatusCurrency($id,$status){
        $currencyDTO = $this->currencyService->load($id);
        $currencyDTO->enabled = ($status == 'enabled') ? true : false;
        return $this->currencyService->save($currencyDTO);
    }

    public function listCurrencies()
    {
        return $this->currencyService->_list();
    }
}

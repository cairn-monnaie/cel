<?php
// src/Cairn/UserCyclosBundle/Service/CurrencyInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;


/**
 *This class contains getters related to currency objects
 *                                                                             
 */
class CurrencyInfo
{

    /**                                                                        
     * Deals with all curreny management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\CurrencyService $currencyService                                            
     */
    private $currencyService;


     /**                                                                        
     * Deals with all group management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\GroupService $groupService                                            
     */
   private $groupService;

    public function __construct()
    {
        $this->currencyService = new Cyclos\CurrencyService();
        $this->groupService = new Cyclos\GroupService();
    }

    /**
     * Provides ID of currency @param
     *
     * @param string $name
     * @return int 
     */
    public function getCurrencyID($name)
    {
        return $this->getCurrencyVO($name)->id;
    }

    public function getCurrencyVO($name)
    {
        $listCurrencies = $this->getListCurrencies();
        foreach($listCurrencies as $currency){
            if($currency->name == $name){
                return $currency;
            }
        }
        return NULL;
    }

    public function getCurrencyDTO($name)
    {
        return $this->currencyService->load($this->getCurrencyID($name));
    }

    public function getCurrencyDTOByID($id)
    {
        return $this->currencyService->load($id);
    }

    public function getCurrencyData($login)
    {
        $id = $this->getCurrencyID($login);
        return $this->currencyService->getData($id);
    }

    public function isInGroup($groupName, $currencyName)
    {
        return ($groupName == $this->getCurrencyData($currencyName)->dto->group->name);
    }


    public function getListCurrencies()
    {
        return $this->currencyService->_list();
    }
}

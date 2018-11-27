<?php
// src/Cairn/UserCyclosBundle/Service/TransferTypeInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;


/**
 *This class contains getters related to transfer types in Cyclos
 *                                                                             
 */
class TransferTypeInfo
{

    /**                                                                        
     * Deals with all transfer types actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\TransferTypeService $transferTypeService                                            
     */
    private $transferTypeService;

    /**                                                                        
     * Deals with all transfer fees actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\TransferFeeService $transferFeeService                                            
     */
    private $transferFeeService;

    public function __construct()
    {
        $this->transferTypeService = new Cyclos\TransferTypeService();
        $this->transferFeeService = new Cyclos\TransferFeeService();
    }

    /**
     *configuration rule : should have only one result, but returns the list for checking
     *
     *@param stdClass $fromAccountType representing org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param stdClass $toAccountType representing org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param string $direction Java class: org.cyclos.model.banking.transfertypes.TransferTypeDirection
     *@param string $nature Java class: org.cyclos.model.banking.transfertypes.TransferTypeNature
     */
    public function getListTransferTypes($fromAccountTypes,$toAccountTypes,$direction,$natures)
    {
        $query = new \stdClass();
        $query->fromAccountTypes = $fromAccountTypes;
        $query->toAccountTypes = $toAccountTypes;
        $query->includeDisabled = true;
        $query->direction = $direction;
        $query->natures = $natures;
        return $this->transferTypeService->search($query)->pageItems;
    }


    /**
     * Provides ID of transferType @param
     *
     * @param string $name
     * @param string $fromNature nature of the debtor account type
     * @param string $toNature nature of the creditor account type
     * @return int 
     */
    public function getTransferTypeID($name,$fromNature, $toNature, $currency)
    {
        $query = new \stdClass();
        $query->currency = $currency;
        $query->fromNature = $fromNature;
        $query->toNature =   $toNature;
        $res = $this->transferTypeService->search($query);

        foreach($res->pageItems as $item){
            if($item->name == $name || $item->internalName == $name){
                return $item->id;
            }
        }
        return NULL;
    }

    /**
     *
     *
     */
    public function getTransferTypeVO($transferTypeDTO)
    {
        $query = new \stdClass();
        $query->includeDisabled = true;
        $query->fromAccountTypes = $transferTypeDTO->from;
        $query->toAccountTypes  = $transferTypeDTO->to;
        $list =  $this->transferTypeService->search($query);

        foreach($list->pageItems as $item)
        {
            if($item->id == $transferTypeDTO->id)
            {
                return $item;
            }
        }
        return NULL;

    }

    /**
     * get the transfer type VO with $id 
     *
     * The only way to get a transfer type VO object is to use the search function of the transferType service. That's why we use another
     * function with parameters to filter before using transfer type's id
     *
     *@param long int $id 
     *@param AccountTypeVO $fromAccountType
     *@param AccountTypeVO $toAccountType
     *@param AccountTypeDirection $direction
     *@return TransferTypeVO
     */ 
    public function getTransferTypeVOByID($fromAccountType,$toAccountType, $direction,$id)
    {
        $list = $this->getListTransferTypes($fromAccountType, $toAccountType, $direction,'PAYMENT');
        foreach($list as $transferType){
            if($transferType->id == $id){
                return $transferType;
            }
        }
        return NULL;


    }

    /**
     * Loads the transfer type DTO with $id 
     *
     *
     *@param long int $id 
     */
    public function getTransferTypeDTOByID($id)
    {
        return $this->transferTypeService->load($id);
    }
}

<?php
// src/Cairn/UserCyclosBundle/Service/AccountTypeInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to account type objects
 *                                                                             
 */
class AccountTypeInfo
{

     /**                                                                        
     * Deals with all account type management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\AccountTypeService $accountTypeService                                            
     */
   private $accountTypeService;


     /**                                                                        
     * Deals with  management of all products assigned to groups
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ProductsGroupService $productsGroupService                                            
     */
   private $productsGroupService;


     /**                                                                        
     * Deals with all individually assigned products management 
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ProductsUserService $productsUserService                                            
     */
   private $productsUserService;

    public function __construct()
    {
        $this->accountTypeService = new Cyclos\AccountTypeService();
        $this->productsGroupService = new Cyclos\ProductsGroupService();
        $this->productsUserService = new Cyclos\ProductsUserService();
    }

    /**
     * Provides ID of accountType @param
     *
     * @param string $name
     * @param string $nature
     * @param stdClass|int $currencyVO
     * @return int 
     */
    public function getAccountTypeID($name,$currencyVO,$nature)
    {
        $query = new \stdClass();
        $query->name = $name;
        $query->currency = $currencyVO;
        $query->nature = $nature;
        $res = $this->accountTypeService->search($query);
        
        if(sizeof($res->pageItems) == 0){
            return NULL;
        }
        return $res->pageItems[0]->id;
    }

    public function getAccountTypeVO($accountType)
    {
        $query = new \stdClass();
        $query->currency = $accountType->currency;
        $query->nature = $accountType->nature;
        $list = $this->accountTypeService->search($query);
        
        if(sizeof($list->pageItems) == 0){
            return NULL;
        }

        foreach ($list->pageItems as $item)
        {
            if($item->id == $accountType->id)
            {
                return $item;
            }
        }
        return NULL;

    }

    public function getAccountTypeData($name,$currency,$nature)
    {
        $id = $this->getAccountTypeID($name,$currency,$nature);
        return $this->accountTypeService->getData($id);
    }

     /*
     * get account type DTO
     * @param string $name
     * @return DTO
     */

    public function getAccountTypeDTO($name,$currency,$nature)
    {
        $id = $this->getAccountTypeID($name,$currency,$nature);
        if($id == NULL){
            return $id;
        }
        return $this->accountTypeService->load($id);
    }

    /**
     *Gets a list of account types with given parameters
     *
     *@param stdClass $currency representing Java type: org.cyclos.model.banking.currencies.CurrencyVO
     *@param string   $nature
     *@return stdClass representing Java type: org.cyclos.utils.Page of Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     */
    public function getListAccountTypes($currency,$nature = NULL)
    {
        $query = new \stdClass();
        $query->currency = $currency;
        $query->nature = $nature;
        $res = $this->accountTypeService->search($query);
        
        if(sizeof($res->pageItems) == 0){
            return NULL;
        }
        return $res->pageItems;

    }

    /**
     * Loads the account type DTO with ID $id
     *
     * @param int $id Account type's ID
     * @return stdClass representing  Java type: org.cyclos.model.banking.accounttypes.AccountTypeDTO
     */
    public function getAccountTypeDTOByID($id)
    {
        return $this->accountTypeService->load($id);
    }

    /**
     *Returns true if the product associated to account type $accountTypeID is assigned to the group with $groupID, false otherwise
     *
     *@param int $accountTypeID account type's ID
     *@param int $groupID group's ID
     *@return ActiveUserProductsData
     */
    public function groupHasAssignedProduct($accountTypeID, $groupID)
    {
        $activeProducts =  $this->productsGroupService->getActiveProducts($groupID,NULL,NULL);

        foreach($activeProducts->accounts as $activeUserAccountData){
            if($activeUserAccountData->userAccount->id == $accountTypeID){
                return true; 
            }
        }
        return false;
    }

    /**
     *Returns true if the product associated to account type $accountTypeID is assigned to the user with $userID, false otherwise
     *
     *@param int $accountTypeID account type's ID
     *@param int $userID user's ID
     *@return ActiveUserProductsData
     */
    public function userHasAssignedProduct($accountTypeID, $userID)
    {
        $activeProducts =  $this->productsUserService->getActiveProducts($userID,NULL,NULL);

        foreach($activeProducts->accounts as $activeUserAccountData){
            if($activeUserAccountData->userAccount->id == $accountTypeID){
                return true; 
            }
        }
        return false;
    }

    /**
     *Returns true if $name does not include another account type's name, or is not included.
     *
     *This function is used while creating an account type, to ensure that the name inputed by the administrator won't confuse Cyclos.
     *For now, a product has necessarily the same name than its associated account type. Therefore, to retrieve a product, we can look for
     *it using its account type's name. For this reason, having two account types so that their name are included can confuse Cyclos.
     *@param string $name
     *@return boolean
     */
    public function hasAvailableName($name)
    {
        $query = new \stdClass();
        $accountTypesVO = $this->accountTypeService->search($query)->pageItems;

        foreach($accountTypesVO as $accountTypeVO){
            if( (preg_match("#".$name."#",$accountTypeVO->name)) || (preg_match("#".$accountTypeVO->name."#",$name))){
                return false;
            }
        }
        return true;
    }


}

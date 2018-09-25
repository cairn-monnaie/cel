<?php
// src/Cairn/UserCyclosBundle/Service/AccountInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

use Cairn\UserCyclosBundle\Service\AccountTypeInfo;

/**
 *This class contains getters related to accounts in Cyclos
 *                                                                             
 */
class AccountInfo
{

    /**                                                                        
     * Deals with all accounts management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\AccountService $accountService                                            
     */
    private $accountService;

    /**                                                                        
     * UserCyclosBundle service containing getters related to account types 
     *@var AccountTypeInfo $accountTypeInfo
     */
    private $accountTypeInfo;

    public function __construct(AccountTypeInfo $accountTypeInfo)
    {
        $this->accountService = new Cyclos\AccountService();
        $this->accountTypeInfo = $accountTypeInfo;
    }

    /**
     *Returns the history of account with ID $accountID during period $period
     *
     *The account history getter is made of two functions provided in the AccountService class of Cyclos PHP WebServices. 
     *They can both be applied with the same query object and parameters hydration. One retrieve a list of transfers(more exactly entries)
     *whereas the other one provides the status of the account.
     *
     *@param int $accountID Cyclos ID of the account
     *@param stdClass $period Two properties : 'begin' and 'end', strings with format 'Y-m-d'
     *@param int $minAmount
     *@param int $maxAmount
     *@param string $description
     *@param int pageSize Number of AccountHistoryEntryVO objects to return in the response
     *@return stdClass containing two objects : 
     *   _Java type: org.cyclos.model.banking.accounts.AccountHistoryStatusVO 
     *   _Java type: org.cyclos.utils.Page of Java type: org.cyclos.model.banking.accounts.AccountHistoryEntryVO
     */
    public function getAccountHistory($accountID, $period,$minAmount = NULL ,$maxAmount = NULL,$description = NULL,$direction = NULL,$pageSize = NULL)
    {
        $query = new \stdClass();
        $query->account = $accountID;
        $query->direction = $direction;
        $query->orderBy = 'DATE_ASC';
        $query->period = $period;

        $query->amount = new \stdClass();
        $query->amount->min = $minAmount;
        $query->amount->max = $maxAmount;

        $query->description = $description;
        $query->pageSize = $pageSize;

        $response = new \stdClass();
        $response->transactions = $this->accountService->searchAccountHistory($query)->pageItems;
        $response->status = $this->accountService->getAccountHistoryStatus($query);

        return $response;

    }

    /**
     *Returns the debit Account( System account with unlimited negative balance)
     *
     *This account is special in the project, as it is the only account with unlimited negative balance limit. It is used everywhere but
     *will be neither seen or editable by any administrator in the system. It is essential for the platform to work correctly, as it is 
     *used to justify credits/debits from/to an account if no other user is involved(e.g for withdrawals/deposits). 
     *
     *For this reason, a specific function is provided to get this specific system account
     *
     *@return stdClass representing Java type: org.cyclos.model.banking.accounts.AccountVO 
     *@throws \Exception No system account with unlimited balance. This should never happen, justifying throwing an exception
     */
    public function getDebitAccount()
    {
        $systemAccounts = $this->accountService->getAccountsSummary('SYSTEM',NULL);
        foreach($systemAccounts as $account){                                  
            if($account->unlimited){                                           
                $debitAccount = $account;                                      
            }                                                                  
        }                                                                      

        if(!$debitAccount){                                                    
            throw new \Exception('Compte de débit inexistant <=> aucun compte système illimité.');               
        }                                                                      
        return $debitAccount; 
    }

    /*
     *Gets the accounts of the user with cyclos ID $userID
     * 
     * This function returns only the accounts such that their accountType's product is associated to the user(individually or a group
     * the user belongs to). 
     * In case of system accounts : the debitAccount(system account with unlimited balance) is removed, as it is useful for internal 
     * proper functioning but it is not supposed to be reachable by any user.
     *
     *@param int      $userID ID of the user the accounts belong to
     *@param stdClass $dateTime date and time requested to get account status
     *@throws Cyclos\ServiceException
     *@return array List of Java type: org.cyclos.model.banking.accounts.AccountWithStatusVO 
     */
    public function getAccountsSummary($userID, $dateTime = NULL)
    {
        $accounts =  $this->accountService->getAccountsSummary($userID, $dateTime);
        $nbAccounts = count($accounts);              

        if($nbAccounts != 0){
            if($accounts[0]->type->nature == 'USER'){
                for($index = 0; $index < $nbAccounts; $index++){                   
                    if(!$this->accountTypeInfo->userHasAssignedProduct($accounts[$index]->type->id,$userID)){            
                        unset($accounts[$index]);                              
                    }                                                              
                }   

            }
            else{//SYSTEM
                for($index = 0; $index < $nbAccounts; $index++){                   
                    if($accounts[$index]->unlimited){            
                        unset($accounts[$index]);                              
                    }                                                              
                }   
            }
        }

        return $accounts;
    }

    /**
     *Returns true if the user with ID $userID owns the account with ID $accountID, returns false otherwise.
     *
     *@param int $userID ID of the user who owns the account $accountID
     *@param int $accountID Account's ID
     *@return bool
     */
    public function hasAccount($userID, $accountID)
    {
        $userAccounts = $this->getAccountsSummary($userID,NULL);
        foreach($userAccounts as $account){
            if($account->id == $accountID){
                return true;
            }   
        }
        return false;
    }

    /*
     *  Get the account by its ID
     *
     *@return stdClass representing Java type: org.cyclos.model.banking.accounts.AccountWithStatusVO 
     */
    public function getAccountByID($accountID, $dateTime = NULL)
    {
        return $this->accountService->getAccountWithStatus($accountID,NULL,NULL);
    }

    /**
     *Returns the default account for a given user.
     *
     *@todo : define default account if several accounts exist
     *@param int $userID ID of the user who owns the account $accountID
     *@return stdClass representing Java type: org.cyclos.model.banking.accounts.AccountWithStatusVO 
     */
    public function getDefaultAccount($userID)
    {
        $accounts = $this->getAccountsSummary($userID,NULL);
        if(count($accounts) == 1){
            return $accounts[0];
        }else{
            return $accounts[0];
        }
    }

}

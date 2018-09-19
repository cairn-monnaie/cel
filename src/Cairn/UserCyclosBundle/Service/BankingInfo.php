<?php
// src/Cairn/UserCyclosBundle/Service/BankingInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to banking operations
 *                                                                             
 */
class BankingInfo
{
    /**
     * Deals with all transactions management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\TransactionService $transactionService                                            
     */
    private $transactionService;

    /**
     * Deals with all scheduled payments management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\ScheduledPaymentService $scheduledPaymentService                                            
     */
    private $scheduledPaymentService;

    /**
     * Deals with all recurring payments management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\RecurringPaymentService $recurringPaymentService                                            
     */
    private $recurringPaymentService;

    /**
     * Deals with all simple payments management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\PaymentService $paymentService                                            
     */
    private $paymentService;

    /**
     * Deals with all transfers management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\TransferService $transferService                                            
     */
    private $transferService;

    public function __construct()
    {
        $this->transactionService = new Cyclos\TransactionService();
        $this->scheduledPaymentService = new Cyclos\ScheduledPaymentService();
        $this->recurringPaymentService = new Cyclos\RecurringPaymentService();
        $this->paymentService = new Cyclos\PaymentService();
        $this->transferService = new Cyclos\TransferService();

    }

    /**
     *@see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransactionService.html#getPaymentData(org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.transfertypes.TransferTypeVO)
     */
    public function getPaymentData($from,$to,$transferType)
    {
        return $this->transactionService->getPaymentData($from,$to,$transferType);
    }

    /**
     *@see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransactionService.html#getData(org.cyclos.model.banking.transactions.TransactionVO)
     */
    public function getTransactionDataByID($id)
    {
        return $this->transactionService->getData($id);

    }

    /**
      * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransactionService.html#load(java.lang.Long)
     */
    public function getTransactionByID($id)
    {
        return $this->transactionService->load($id);
    }

    /**
      * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransferService.html#load(java.lang.Long)
     */
    public function getTransferByID($id)
    {
        return $this->transferService->load($id);
    }

    /**
      * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransferService.html#getData(java.lang.Long)
     */
    public function getTransferData($id)
    {
        return $this->transferService->getData($id);
    }

    /**
      * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransactionService.html#loadByTransactionNumber(java.lang.Long)
     */
    public function getTransaction($transactionNumber)
    {
        return $this->transactionService->loadByTransactionNumber($transactionNumber);
    }

    /**
      * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/TransferService.html#loadByTransactionNumber(java.lang.Long)
     */
    public function getTransferByTransactionNumber($transactionNumber)
    {
        return $this->transferService->loadByTransactionNumber($transactionNumber);
    }

    /**
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#getData(java.lang.Long)
    */
    public function getRecurringTransactionDataByID($id)
    {
        return $this->recurringPaymentService->getData($id);
    }

    /**
     *Hydrates the empty query with different compuslary/optional parameters
     *
     *@param stdClass|int $ownerVO Either InternalAccountOwner  or itd ID
     *@param array of stdClass representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param array of strings Java type: org.cyclos.model.banking.transactions.TransactionNature
     *@param array of arrays of strings Java type : interface TransactionStatus :
     *       example : array( array('PROCESSED','SCHEDULED') , 'OPEN', array('BLOCKED','CANCELED'))
     *@param string $description
     *@param string $orderBy Java type: org.cyclos.model.banking.transactions.TransactionOrderBy
     *@param stdClass $period Java type : org.cyclos.model.utils.DatePeriodDTO
     *@param int $pageSize number of responses
     */
    public function hydrateQuery($ownerVO,$accountTypesVO,$natures,$statuses,$description,$orderBy, $period, $pageSize)
    {
        $query             = new \stdClass();
        $query->owner       = $ownerVO;
        $query->accountTypes = $accountTypesVO;
        $query->natures   = $natures; 
        //the array of statuses will be filled in this order : payment request status/recurring payment status/ scheduled payment status
        $query->statuses = $statuses;
        $query->description = $description;
        $query->orderBy    = ($orderBy == NULL) ? 'DATE_DESC' : $orderBy;
        $query->period = $period;
        $query->pageSize = $pageSize;
        
        return $query;
    }

    /**
     *Returns all transactions matching the given query hydrated with parameters 
     *
     *@param stdClass|int $ownerVO Either InternalAccountOwner  or itd ID
     *@param array of stdClass representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param array of strings Java type: org.cyclos.model.banking.transactions.TransactionNature
     *@param array of arrays of strings Java type : interface TransactionStatus :
     *       example : array( array('PROCESSED','SCHEDULED') , 'OPEN', array('BLOCKED','CANCELED'))
     *@param string $description
     *@param string $orderBy Java type: org.cyclos.model.banking.transactions.TransactionOrderBy
     *@param stdClass $period Java type : org.cyclos.model.utils.DatePeriodDTO
     *@param int $pageSize number of responses
     */
    public function getTransactions($ownerVO,$accountTypesVO,$natures = NULL,$statuses = NULL,$description, $orderBy = NULL, $period = NULL, $pageSize = NULL)
    {
        $query = $this->hydrateQuery($ownerVO,$accountTypesVO,$natures,$statuses,$description, $orderBy , $period, $pageSize);

        if((count($query->statuses) != 0) && (count($query->statuses) != 3)){
            throw new \Exception('invalid parameter : array $statuses must contain 3 arrays or must be null, ' .count($query->statuses). ' given');
        }

        $query->paymentRequestStatuses = $query->statuses[0];
        $query->recurringPaymentStatuses = $query->statuses[1];
        $query->scheduledPaymentStatuses = $query->statuses[2];

        $res = $this->transactionService->search($query);
        return $res->pageItems;
    }

    /**
     *Returns all recurring transactions matching the given query hydrated with parameters 
     *
     *@param stdClass|int $ownerVO Either InternalAccountOwner  or itd ID
     *@param array of stdClass representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param array of arrays of strings Java type : interface TransactionStatus :
     *       example : array( array('PROCESSED','SCHEDULED') , 'OPEN', array('BLOCKED','CANCELED'))
     *@param string $description
     *@param string $orderBy Java type: org.cyclos.model.banking.transactions.TransactionOrderBy
     *@param stdClass $period Java type : org.cyclos.model.utils.DatePeriodDTO
     *@param int $pageSize number of responses
     */
    public function getRecurringTransactionsDataBy($ownerVO,$accountTypesVO,$statuses,$description,$orderBy = NULL, $period = NULL, $pageSize = NULL)
    {
        $query = $this->hydrateQuery($ownerVO,$accountTypesVO,'RECURRING_PAYMENT',$statuses,$description, $orderBy , $period, $pageSize);

        $transactions = $this->recurringPaymentService->search($query)->pageItems;

        //At this step, $transactions contains instances of RecurringPaymntEntryVO. Not enough data provided. 
        //Therefore, using id, we get corresponding instances of RecurringPaymentData which contain an attribute "transaction" with much
        //more data
        $detailedTransactions = array();
        foreach($transactions as $transaction){
            $detailedTransactions[] = $this->getRecurringTransactionDataByID($transaction->id)->transaction;
        }
        return $detailedTransactions;
    }

    /**
     *Returns all scheduled transactions matching the given query hydrated with parameters 
     *
     *@param stdClass|int $ownerVO Either InternalAccountOwner  or itd ID
     *@param array of stdClass representing Java type: org.cyclos.model.banking.accounttypes.AccountTypeVO
     *@param array of arrays of strings Java type : interface TransactionStatus :
     *       example : array( array('PROCESSED','SCHEDULED') , 'OPEN', array('BLOCKED','CANCELED'))
     *@param string $description
     *@param string $orderBy Java type: org.cyclos.model.banking.transactions.TransactionOrderBy
     *@param stdClass $period Java type : org.cyclos.model.utils.DatePeriodDTO
     *@param int $pageSize number of responses
     */
    public function getInstallments($ownerVO,$accountTypesVO,$statuses = NULL,$description, $orderBy = NULL, $period = NULL, $pageSize = NULL)
    {
        $query = $this->hydrateQuery($ownerVO,$accountTypesVO,'SCHEDULED_PAYMENT',$statuses,$description,$orderBy , $period, $pageSize);

        //one array with all desired statuses is required
        $query->status = $query->statuses;
        unset($query->statuses); //for clarity when reading the object 

        return $this->scheduledPaymentService->searchInstallments($query)->pageItems;
    }


    /**
     *Returns data on a single installment with ID $id
     *
     *@see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#getData(java.lang.Long    )
     */
    public function getInstallmentData($id)
    {
        return $this->scheduledPaymentService->getData($id);
    }

}

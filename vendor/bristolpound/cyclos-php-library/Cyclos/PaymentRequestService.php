<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class PaymentRequestService extends Service {

    function __construct() {
        parent::__construct('paymentRequestService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.AcceptPaymentRequestDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentRequestVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#accept(org.cyclos.model.banking.transactions.AcceptPaymentRequestDTO)
     */
    public function accept($params) {
        return $this->__run('accept', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.PaymentRequestActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#cancel(org.cyclos.model.banking.transactions.PaymentRequestActionDTO)
     */
    public function cancel($params) {
        $this->__run('cancel', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.PaymentRequestActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#deny(org.cyclos.model.banking.transactions.PaymentRequestActionDTO)
     */
    public function deny($params) {
        $this->__run('deny', array($params));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.PaymentRequestOverviewQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#exportPaymentRequestsToCSV(org.cyclos.model.banking.transactions.PaymentRequestOverviewQuery)
     */
    public function exportPaymentRequestsToCSV($query) {
        return $this->__run('exportPaymentRequestsToCSV', array($query));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.PaymentRequestData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param payee Java type: org.cyclos.model.banking.accounts.InternalAccountOwner     * @param payer Java type: org.cyclos.model.banking.accounts.InternalAccountOwner
     * @return Java type: org.cyclos.model.banking.transactions.RequestPaymentData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#getRequestData(org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.accounts.InternalAccountOwner)
     */
    public function getRequestData($payee, $payer) {
        return $this->__run('getRequestData', array($payee, $payer));
    }
    
    /**
     * @param payee Java type: org.cyclos.model.banking.accounts.InternalAccountOwner     * @param payer Java type: org.cyclos.model.banking.accounts.InternalAccountOwner
     * @return Java type: org.cyclos.model.banking.transactions.RequestPaymentPayerData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#getRequestPaymentPayerData(org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.accounts.InternalAccountOwner)
     */
    public function getRequestPaymentPayerData($payee, $payer) {
        return $this->__run('getRequestPaymentPayerData', array($payee, $payer));
    }
    
    /**
     * @param payee Java type: org.cyclos.model.banking.accounts.InternalAccountOwner     * @param payer Java type: org.cyclos.model.banking.accounts.InternalAccountOwner     * @param transferType Java type: org.cyclos.model.banking.transfertypes.TransferTypeVO
     * @return Java type: org.cyclos.model.banking.transactions.RequestPaymentTypeData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#getRequestPaymentTypeData(org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.transfertypes.TransferTypeVO)
     */
    public function getRequestPaymentTypeData($payee, $payer, $transferType) {
        return $this->__run('getRequestPaymentTypeData', array($payee, $payer, $transferType));
    }
    
    /**

     * @return Java type: org.cyclos.model.banking.accounts.PaymentRequestSearchOverviewData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#getSearchOverviewData()
     */
    public function getSearchOverviewData() {
        return $this->__run('getSearchOverviewData', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.AcceptPaymentRequestPreviewVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#previewAccept(java.lang.Long)
     */
    public function previewAccept($id) {
        return $this->__run('previewAccept', array($id));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.AcceptPaymentRequestDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentRequestVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#reschedule(org.cyclos.model.banking.transactions.AcceptPaymentRequestDTO)
     */
    public function reschedule($params) {
        return $this->__run('reschedule', array($params));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.PaymentRequestQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#search(org.cyclos.model.banking.transactions.PaymentRequestQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.PaymentRequestOverviewQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#searchOverview(org.cyclos.model.banking.transactions.PaymentRequestOverviewQuery)
     */
    public function searchOverview($params) {
        return $this->__run('searchOverview', array($params));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.SendPaymentRequestDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentRequestVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/PaymentRequestService.html#send(org.cyclos.model.banking.transactions.SendPaymentRequestDTO)
     */
    public function send($parameters) {
        return $this->__run('send', array($parameters));
    }
    
}

?>
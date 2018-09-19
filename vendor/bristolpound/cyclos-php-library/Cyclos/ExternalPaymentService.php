<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ExternalPaymentService extends Service {

    function __construct() {
        parent::__construct('externalPaymentService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ExternalPaymentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#cancel(org.cyclos.model.banking.transactions.ExternalPaymentActionDTO)
     */
    public function cancel($params) {
        $this->__run('cancel', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.ExternalPaymentData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param from Java type: org.cyclos.model.banking.accounts.InternalAccountOwner
     * @return Java type: org.cyclos.model.banking.transactions.PerformExternalPaymentData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#getExternalPaymentData(org.cyclos.model.banking.accounts.InternalAccountOwner)
     */
    public function getExternalPaymentData($from) {
        return $this->__run('getExternalPaymentData', array($from));
    }
    
    /**
     * @param from Java type: org.cyclos.model.banking.accounts.InternalAccountOwner     * @param transferType Java type: org.cyclos.model.banking.transfertypes.TransferTypeVO
     * @return Java type: org.cyclos.model.banking.transactions.PerformExternalPaymentTypeData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#getExternalPaymentTypeData(org.cyclos.model.banking.accounts.InternalAccountOwner,%20org.cyclos.model.banking.transfertypes.TransferTypeVO)
     */
    public function getExternalPaymentTypeData($from, $transferType) {
        return $this->__run('getExternalPaymentTypeData', array($from, $transferType));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformExternalPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ExternalPaymentVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#perform(org.cyclos.model.banking.transactions.PerformExternalPaymentDTO)
     */
    public function perform($parameters) {
        return $this->__run('perform', array($parameters));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformExternalPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ExternalPaymentPreviewVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#preview(org.cyclos.model.banking.transactions.PerformExternalPaymentDTO)
     */
    public function preview($parameters) {
        return $this->__run('preview', array($parameters));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.ExternalPaymentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ExternalPaymentService.html#search(org.cyclos.model.banking.transactions.ExternalPaymentQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
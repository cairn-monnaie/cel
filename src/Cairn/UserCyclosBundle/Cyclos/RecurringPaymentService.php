<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class RecurringPaymentService extends Service {

    function __construct() {
        parent::__construct('recurringPaymentService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.RecurringPaymentActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#cancel(org.cyclos.model.banking.transactions.RecurringPaymentActionDTO)
     */
    public function cancel($params) {
        $this->__run('cancel', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.RecurringPaymentData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param recurringPayment Java type: org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.RecurringPaymentVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#perform(org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO)
     */
    public function perform($recurringPayment) {
        return $this->__run('perform', array($recurringPayment));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.RecurringPaymentPreviewVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#preview(org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO)
     */
    public function preview($parameters) {
        return $this->__run('preview', array($parameters));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.RecurringPaymentPreviewVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#previewReceive(org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO)
     */
    public function previewReceive($parameters) {
        return $this->__run('previewReceive', array($parameters));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#print(java.lang.Long)
     */
    public function _print($id) {
        return $this->__run('print', array($id));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.RecurringPaymentQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#printRecurringPayments(org.cyclos.model.banking.transactions.RecurringPaymentQuery)
     */
    public function printRecurringPayments($query) {
        return $this->__run('printRecurringPayments', array($query));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.FailedOccurrenceActionDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#processFailure(org.cyclos.model.banking.transactions.FailedOccurrenceActionDTO)
     */
    public function processFailure($params) {
        return $this->__run('processFailure', array($params));
    }
    
    /**
     * @param recurringPayment Java type: org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.RecurringPaymentVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#receive(org.cyclos.model.banking.transactions.PerformRecurringPaymentDTO)
     */
    public function receive($recurringPayment) {
        return $this->__run('receive', array($recurringPayment));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.RecurringPaymentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/RecurringPaymentService.html#search(org.cyclos.model.banking.transactions.RecurringPaymentQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ScheduledPaymentService extends Service {

    function __construct() {
        parent::__construct('scheduledPaymentService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#block(org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO)
     */
    public function block($params) {
        $this->__run('block', array($params));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.banking.transactions.CalculateInstallmentsDTO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#calculateInstallments(org.cyclos.model.banking.transactions.CalculateInstallmentsDTO)
     */
    public function calculateInstallments($dto) {
        return $this->__run('calculateInstallments', array($dto));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.banking.transactions.CalculateInstallmentsDTO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#calculateInstallmentsReceive(org.cyclos.model.banking.transactions.CalculateInstallmentsDTO)
     */
    public function calculateInstallmentsReceive($dto) {
        return $this->__run('calculateInstallmentsReceive', array($dto));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#cancel(org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO)
     */
    public function cancel($params) {
        $this->__run('cancel', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.ScheduledPaymentData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param scheduledPayment Java type: org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ScheduledPaymentVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#perform(org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO)
     */
    public function perform($scheduledPayment) {
        return $this->__run('perform', array($scheduledPayment));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ScheduledPaymentPreviewVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#preview(org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO)
     */
    public function preview($parameters) {
        return $this->__run('preview', array($parameters));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ScheduledPaymentPreviewVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#previewReceive(org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO)
     */
    public function previewReceive($parameters) {
        return $this->__run('previewReceive', array($parameters));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#print(java.lang.Long)
     */
    public function _print($id) {
        return $this->__run('print', array($id));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.ScheduledPaymentQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#printScheduledPayments(org.cyclos.model.banking.transactions.ScheduledPaymentQuery)
     */
    public function printScheduledPayments($query) {
        return $this->__run('printScheduledPayments', array($query));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentActionDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#processInstallment(org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentActionDTO)
     */
    public function processInstallment($params) {
        return $this->__run('processInstallment', array($params));
    }
    
    /**
     * @param scheduledPayment Java type: org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.ScheduledPaymentVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#receive(org.cyclos.model.banking.transactions.PerformScheduledPaymentDTO)
     */
    public function receive($scheduledPayment) {
        return $this->__run('receive', array($scheduledPayment));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.ScheduledPaymentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#search(org.cyclos.model.banking.transactions.ScheduledPaymentQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#searchInstallments(org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentQuery)
     */
    public function searchInstallments($query) {
        return $this->__run('searchInstallments', array($query));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#settleInstallment(org.cyclos.model.banking.transactions.ScheduledPaymentInstallmentActionDTO)
     */
    public function settleInstallment($params) {
        $this->__run('settleInstallment', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#settleRemaining(org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO)
     */
    public function settleRemaining($params) {
        $this->__run('settleRemaining', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ScheduledPaymentService.html#unblock(org.cyclos.model.banking.transactions.ScheduledPaymentActionDTO)
     */
    public function unblock($params) {
        $this->__run('unblock', array($params));
    }
    
}

?>
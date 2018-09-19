<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class PaymentService extends Service {

    function __construct() {
        parent::__construct('paymentService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transactions.PaymentBarcodeParams
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#barcode(org.cyclos.model.banking.transactions.PaymentBarcodeParams)
     */
    public function barcode($params) {
        return $this->__run('barcode', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.PaymentData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#perform(org.cyclos.model.banking.transactions.PerformPaymentDTO)
     */
    public function perform($parameters) {
        return $this->__run('perform', array($parameters));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentPreviewVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#preview(org.cyclos.model.banking.transactions.PerformPaymentDTO)
     */
    public function preview($parameters) {
        return $this->__run('preview', array($parameters));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentPreviewVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#previewReceive(org.cyclos.model.banking.transactions.PerformPaymentDTO)
     */
    public function previewReceive($parameters) {
        return $this->__run('previewReceive', array($parameters));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#print(java.lang.Long)
     */
    public function _print($id) {
        return $this->__run('print', array($id));
    }
    
    /**
     * @param parameters Java type: org.cyclos.model.banking.transactions.PerformPaymentDTO
     * @return Java type: org.cyclos.model.banking.transactions.PaymentVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#receive(org.cyclos.model.banking.transactions.PerformPaymentDTO)
     */
    public function receive($parameters) {
        return $this->__run('receive', array($parameters));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.transactions.PaymentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/PaymentService.html#search(org.cyclos.model.banking.transactions.PaymentQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
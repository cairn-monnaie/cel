<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class TransferService extends Service {

    function __construct() {
        parent::__construct('transferService');
    }
    
    /**
     * @param vo Java type: org.cyclos.model.banking.transfers.TransferVO
     * @return Java type: org.cyclos.model.banking.transfers.TransferData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferService.html#getData(org.cyclos.model.banking.transfers.TransferVO)
     */
    public function getData($vo) {
        return $this->__run('getData', array($vo));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transfers.TransferVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param transactionNumber Java type: java.lang.String
     * @return Java type: org.cyclos.model.banking.transfers.TransferVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferService.html#loadByTransactionNumber(java.lang.String)
     */
    public function loadByTransactionNumber($transactionNumber) {
        return $this->__run('loadByTransactionNumber', array($transactionNumber));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferService.html#printTransfer(java.lang.Long)
     */
    public function printTransfer($id) {
        return $this->__run('printTransfer', array($id));
    }
    
}

?>
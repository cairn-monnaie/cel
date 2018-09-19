<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ChargebackService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ChargebackService extends Service {

    function __construct() {
        parent::__construct('chargebackService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.banking.transfers.TransferActionDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ChargebackService.html#chargeback(org.cyclos.model.banking.transfers.TransferActionDTO)
     */
    public function chargeback($params) {
        return $this->__run('chargeback', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.transactions.ChargebackData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/ChargebackService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
}

?>
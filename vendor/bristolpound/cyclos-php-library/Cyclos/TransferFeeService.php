<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class TransferFeeService extends Service {

    function __construct() {
        parent::__construct('transferFeeService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param transferType Java type: org.cyclos.model.banking.transfertypes.TransferTypeVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#list(org.cyclos.model.banking.transfertypes.TransferTypeVO)
     */
    public function _list($transferType) {
        return $this->__run('list', array($transferType));
    }
    
    /**
     * @param fee Java type: org.cyclos.model.banking.transferfees.TransferFeeDTO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#listPossibleGeneratedTransferTypes(org.cyclos.model.banking.transferfees.TransferFeeDTO)
     */
    public function listPossibleGeneratedTransferTypes($fee) {
        return $this->__run('listPossibleGeneratedTransferTypes', array($fee));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/TransferFeeService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
}

?>
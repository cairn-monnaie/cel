<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AccountFeeLogService extends Service {

    function __construct() {
        parent::__construct('accountFeeLogService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.banking.accountfees.AccountFeeLogData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param accountFee Java type: org.cyclos.model.banking.accountfees.AccountFeeVO
     * @return Java type: org.cyclos.model.banking.accountfees.AccountFeeLogsSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#getSearchData(org.cyclos.model.banking.accountfees.AccountFeeVO)
     */
    public function getSearchData($accountFee) {
        return $this->__run('getSearchData', array($accountFee));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#listFeeExecutions()
     */
    public function listFeeExecutions() {
        return $this->__run('listFeeExecutions', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#rechargeFailed(java.lang.Long)
     */
    public function rechargeFailed($id) {
        $this->__run('rechargeFailed', array($id));
    }
    
    /**
     * @param accountFee Java type: org.cyclos.model.banking.accountfees.AccountFeeVO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#runNow(org.cyclos.model.banking.accountfees.AccountFeeVO)
     */
    public function runNow($accountFee) {
        return $this->__run('runNow', array($accountFee));
    }
    
    /**
     * @param query Java type: org.cyclos.model.banking.accountfees.AccountFeeLogQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/banking/AccountFeeLogService.html#search(org.cyclos.model.banking.accountfees.AccountFeeLogQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
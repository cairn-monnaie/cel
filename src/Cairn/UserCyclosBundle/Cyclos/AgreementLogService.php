<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/AgreementLogService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AgreementLogService extends Service {

    function __construct() {
        parent::__construct('agreementLogService');
    }
    
    /**
     * @param agreements Java type: java.util.Set
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/AgreementLogService.html#accept(java.util.Set)
     */
    public function accept($agreements) {
        $this->__run('accept', array($agreements));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/AgreementLogService.html#getPendingAgreements()
     */
    public function getPendingAgreements() {
        return $this->__run('getPendingAgreements', array());
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/AgreementLogService.html#list(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function _list($locator) {
        return $this->__run('list', array($locator));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.access.agreementlogs.AgreementLogVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/AgreementLogService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AlertService extends Service {

    function __construct() {
        parent::__construct('alertService');
    }
    
    /**

     * @return Java type: org.cyclos.model.messaging.alerts.SystemAlertSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html#getSystemSearchData()
     */
    public function getSystemSearchData() {
        return $this->__run('getSystemSearchData', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Set
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html#removeAll(java.util.Set)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param alertQuery Java type: org.cyclos.model.messaging.alerts.SystemAlertQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html#searchSystem(org.cyclos.model.messaging.alerts.SystemAlertQuery)
     */
    public function searchSystem($alertQuery) {
        return $this->__run('searchSystem', array($alertQuery));
    }
    
    /**
     * @param alertQuery Java type: org.cyclos.model.messaging.alerts.UserAlertQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/messaging/AlertService.html#searchUser(org.cyclos.model.messaging.alerts.UserAlertQuery)
     */
    public function searchUser($alertQuery) {
        return $this->__run('searchUser', array($alertQuery));
    }
    
}

?>
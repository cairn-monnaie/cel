<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ErrorLogService extends Service {

    function __construct() {
        parent::__construct('errorLogService');
    }
    
    /**

     * @return Java type: org.cyclos.model.messaging.errorlogs.ErrorLogSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.messaging.errorlogs.ErrorLogDetailedVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Set
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html#removeAll(java.util.Set)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param query Java type: org.cyclos.model.messaging.errorlogs.ErrorLogQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/ErrorLogService.html#search(org.cyclos.model.messaging.errorlogs.ErrorLogQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
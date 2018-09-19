<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/EntityLogService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class EntityLogService extends Service {

    function __construct() {
        parent::__construct('entityLogService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.system.entitylogs.EntityPropertyLogVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/EntityLogService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param params Java type: org.cyclos.model.system.entitylogs.EntityPropertyLogQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/EntityLogService.html#search(org.cyclos.model.system.entitylogs.EntityPropertyLogQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
}

?>
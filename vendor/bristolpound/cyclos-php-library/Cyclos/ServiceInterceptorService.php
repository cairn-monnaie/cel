<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ServiceInterceptorService extends Service {

    function __construct() {
        parent::__construct('serviceInterceptorService');
    }
    
    /**
     * @param network Java type: org.cyclos.model.system.networks.NetworkVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#disableAllInterceptorsInNetwork(org.cyclos.model.system.networks.NetworkVO)
     */
    public function disableAllInterceptorsInNetwork($network) {
        $this->__run('disableAllInterceptorsInNetwork', array($network));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#list()
     */
    public function _list() {
        return $this->__run('list', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param ids Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/ServiceInterceptorService.html#saveOrder(java.util.List)
     */
    public function saveOrder($ids) {
        $this->__run('saveOrder', array($ids));
    }
    
}

?>
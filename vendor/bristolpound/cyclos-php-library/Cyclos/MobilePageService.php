<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class MobilePageService extends Service {

    function __construct() {
        parent::__construct('mobilePageService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#getContent(java.lang.Long)
     */
    public function getContent($id) {
        return $this->__run('getContent', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param configuration Java type: org.cyclos.model.system.configurations.ConfigurationVO
     * @return Java type: org.cyclos.model.contentmanagement.mobilepages.MobilePagesListData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#getListData(org.cyclos.model.system.configurations.ConfigurationVO)
     */
    public function getListData($configuration) {
        return $this->__run('getListData', array($configuration));
    }
    
    /**
     * @param configuration Java type: org.cyclos.model.system.configurations.ConfigurationVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#list(org.cyclos.model.system.configurations.ConfigurationVO)
     */
    public function _list($configuration) {
        return $this->__run('list', array($configuration));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param ids Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/MobilePageService.html#saveOrder(java.util.List)
     */
    public function saveOrder($ids) {
        $this->__run('saveOrder', array($ids));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class RecordCustomFieldService extends Service {

    function __construct() {
        parent::__construct('recordCustomFieldService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO     * @param customField Java type: org.cyclos.model.system.fields.CustomFieldVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#linkShared(org.cyclos.model.users.recordtypes.RecordTypeVO,%20org.cyclos.model.system.fields.CustomFieldVO)
     */
    public function linkShared($recordType, $customField) {
        $this->__run('linkShared', array($recordType, $customField));
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#list(org.cyclos.model.users.recordtypes.RecordTypeVO)
     */
    public function _list($recordType) {
        return $this->__run('list', array($recordType));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#listShared()
     */
    public function listShared() {
        return $this->__run('listShared', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param customFieldIds Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#saveOrder(java.util.List)
     */
    public function saveOrder($customFieldIds) {
        $this->__run('saveOrder', array($customFieldIds));
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO     * @param customFields Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#saveOrderOnType(org.cyclos.model.users.recordtypes.RecordTypeVO,%20java.util.List)
     */
    public function saveOrderOnType($recordType, $customFields) {
        $this->__run('saveOrderOnType', array($recordType, $customFields));
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO     * @param customField Java type: org.cyclos.model.system.fields.CustomFieldVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldService.html#unlinkShared(org.cyclos.model.users.recordtypes.RecordTypeVO,%20org.cyclos.model.system.fields.CustomFieldVO)
     */
    public function unlinkShared($recordType, $customField) {
        $this->__run('unlinkShared', array($recordType, $customField));
    }
    
}

?>
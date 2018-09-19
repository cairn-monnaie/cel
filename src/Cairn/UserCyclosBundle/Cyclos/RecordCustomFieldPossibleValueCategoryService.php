<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class RecordCustomFieldPossibleValueCategoryService extends Service {

    function __construct() {
        parent::__construct('recordCustomFieldPossibleValueCategoryService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param customField Java type: org.cyclos.model.system.fields.CustomFieldVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#list(org.cyclos.model.system.fields.CustomFieldVO)
     */
    public function _list($customField) {
        return $this->__run('list', array($customField));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param up Java type: boolean
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#move(java.lang.Long,%20boolean)
     */
    public function move($id, $up) {
        $this->__run('move', array($id, $up));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param dtos Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#saveCategories(java.util.List)
     */
    public function saveCategories($dtos) {
        $this->__run('saveCategories', array($dtos));
    }
    
    /**
     * @param customFieldCategoryIds Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordCustomFieldPossibleValueCategoryService.html#saveOrder(java.util.List)
     */
    public function saveOrder($customFieldCategoryIds) {
        $this->__run('saveOrder', array($customFieldCategoryIds));
    }
    
}

?>
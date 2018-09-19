<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class RecordService extends Service {

    function __construct() {
        parent::__construct('recordService');
    }
    
    /**
     * @param query Java type: org.cyclos.model.users.records.RecordQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#exportToCSV(org.cyclos.model.users.records.RecordQuery)
     */
    public function exportToCSV($query) {
        return $this->__run('exportToCSV', array($query));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.banking.accounts.AccountOwnerLocator
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getRecordTypeCount(org.cyclos.model.banking.accounts.AccountOwnerLocator)
     */
    public function getRecordTypeCount($locator) {
        return $this->__run('getRecordTypeCount', array($locator));
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.records.RecordSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getSearchData(org.cyclos.model.users.recordtypes.RecordTypeVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getSearchData($recordType, $locator) {
        return $this->__run('getSearchData', array($recordType, $locator));
    }
    
    /**

     * @return Java type: org.cyclos.model.users.records.SharedRecordFieldsSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getSharedFieldsSearchData()
     */
    public function getSharedFieldsSearchData() {
        return $this->__run('getSharedFieldsSearchData', array());
    }
    
    /**
     * @param recordType Java type: org.cyclos.model.users.recordtypes.RecordTypeVO     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.records.TiledRecordsData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#getTiledRecordsData(org.cyclos.model.users.recordtypes.RecordTypeVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getTiledRecordsData($recordType, $locator) {
        return $this->__run('getTiledRecordsData', array($recordType, $locator));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.users.records.RecordVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#loadVO(java.lang.Long)
     */
    public function loadVO($id) {
        return $this->__run('loadVO', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param query Java type: org.cyclos.model.users.records.RecordQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/RecordService.html#search(org.cyclos.model.users.records.RecordQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class CustomOperationService extends Service {

    function __construct() {
        parent::__construct('customOperationService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param customOperation Java type: org.cyclos.model.system.operations.CustomOperationVO     * @param ad Java type: org.cyclos.model.marketplace.advertisements.BasicAdVO
     * @return Java type: org.cyclos.model.system.operations.RunCustomOperationData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunAdData(org.cyclos.model.system.operations.CustomOperationVO,%20org.cyclos.model.marketplace.advertisements.BasicAdVO)
     */
    public function getRunAdData($customOperation, $ad) {
        return $this->__run('getRunAdData', array($customOperation, $ad));
    }
    
    /**
     * @param customOperation Java type: org.cyclos.model.system.operations.CustomOperationVO
     * @return Java type: org.cyclos.model.system.operations.RunCustomOperationData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunData(org.cyclos.model.system.operations.CustomOperationVO)
     */
    public function getRunData($customOperation) {
        return $this->__run('getRunData', array($customOperation));
    }
    
    /**
     * @param customOperation Java type: org.cyclos.model.system.operations.CustomOperationVO     * @param record Java type: org.cyclos.model.users.records.RecordVO
     * @return Java type: org.cyclos.model.system.operations.RunCustomOperationData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunRecordData(org.cyclos.model.system.operations.CustomOperationVO,%20org.cyclos.model.users.records.RecordVO)
     */
    public function getRunRecordData($customOperation, $record) {
        return $this->__run('getRunRecordData', array($customOperation, $record));
    }
    
    /**
     * @param customOperation Java type: org.cyclos.model.system.operations.CustomOperationVO     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.system.operations.RunCustomOperationData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunUserData(org.cyclos.model.system.operations.CustomOperationVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getRunUserData($customOperation, $user) {
        return $this->__run('getRunUserData', array($customOperation, $user));
    }
    
    /**
     * @param ad Java type: org.cyclos.model.marketplace.advertisements.BasicAdVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunnableOperationsForAd(org.cyclos.model.marketplace.advertisements.BasicAdVO)
     */
    public function getRunnableOperationsForAd($ad) {
        return $this->__run('getRunnableOperationsForAd', array($ad));
    }
    
    /**
     * @param record Java type: org.cyclos.model.users.records.RecordVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunnableOperationsForRecord(org.cyclos.model.users.records.RecordVO)
     */
    public function getRunnableOperationsForRecord($record) {
        return $this->__run('getRunnableOperationsForRecord', array($record));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunnableOperationsForSystem()
     */
    public function getRunnableOperationsForSystem() {
        return $this->__run('getRunnableOperationsForSystem', array());
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#getRunnableOperationsForUser(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getRunnableOperationsForUser($locator) {
        return $this->__run('getRunnableOperationsForUser', array($locator));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#list()
     */
    public function _list() {
        return $this->__run('list', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param params Java type: org.cyclos.model.system.operations.RunCustomOperationDTO
     * @return Java type: org.cyclos.model.system.operations.RunCustomOperationResult
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#run(org.cyclos.model.system.operations.RunCustomOperationDTO)
     */
    public function run($params) {
        return $this->__run('run', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.system.operations.RunExternalRedirectCallbackParams
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#runExternalRedirectCallback(org.cyclos.model.system.operations.RunExternalRedirectCallbackParams)
     */
    public function runExternalRedirectCallback($params) {
        return $this->__run('runExternalRedirectCallback', array($params));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param ids Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/CustomOperationService.html#saveOrder(java.util.List)
     */
    public function saveOrder($ids) {
        $this->__run('saveOrder', array($ids));
    }
    
}

?>
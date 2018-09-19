<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class BulkActionService extends Service {

    function __construct() {
        parent::__construct('bulkActionService');
    }
    
    /**
     * @param bulkActionId Java type: java.lang.Long
     * @return Java type: int
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#cancel(java.lang.Long)
     */
    public function cancel($bulkActionId) {
        return $this->__run('cancel', array($bulkActionId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param bulkActionUserId Java type: java.lang.Long
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getErrorStackTrace(java.lang.Long)
     */
    public function getErrorStackTrace($bulkActionUserId) {
        return $this->__run('getErrorStackTrace', array($bulkActionUserId));
    }
    
    /**

     * @return Java type: org.cyclos.model.users.bulkactions.BulkActionsSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param params Java type: org.cyclos.model.users.bulkactions.BulkActionDataParams
     * @return Java type: org.cyclos.model.users.bulkactions.BulkActionSearchUsersData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getSearchUsersData(org.cyclos.model.users.bulkactions.BulkActionDataParams)
     */
    public function getSearchUsersData($params) {
        return $this->__run('getSearchUsersData', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.users.bulkactions.BulkActionStatusVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#getStatus(java.lang.Long)
     */
    public function getStatus($id) {
        return $this->__run('getStatus', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param params Java type: org.cyclos.model.users.bulkactions.BulkActionQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#search(org.cyclos.model.users.bulkactions.BulkActionQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.users.bulkactions.BulkActionUserQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BulkActionService.html#searchUsers(org.cyclos.model.users.bulkactions.BulkActionUserQuery)
     */
    public function searchUsers($params) {
        return $this->__run('searchUsers', array($params));
    }
    
}

?>
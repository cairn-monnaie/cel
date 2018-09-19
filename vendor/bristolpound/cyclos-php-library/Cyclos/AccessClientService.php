<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AccessClientService extends Service {

    function __construct() {
        parent::__construct('accessClientService');
    }
    
    /**
     * @param activationCode Java type: java.lang.String     * @param prefix Java type: java.lang.String
     * @return Java type: org.cyclos.model.access.clients.ActivateAccessClientDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#activate(java.lang.String,%20java.lang.String)
     */
    public function activate($activationCode, $prefix) {
        return $this->__run('activate', array($activationCode, $prefix));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.clients.AccessClientActionParams
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#block(org.cyclos.model.access.clients.AccessClientActionParams)
     */
    public function block($params) {
        $this->__run('block', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.clients.AccessClientActionParams
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getActivationCode(org.cyclos.model.access.clients.AccessClientActionParams)
     */
    public function getActivationCode($params) {
        return $this->__run('getActivationCode', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param principalType Java type: org.cyclos.model.access.principaltypes.PrincipalTypeVO     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.access.clients.AccessClientsListData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getListData(org.cyclos.model.access.principaltypes.PrincipalTypeVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getListData($principalType, $user) {
        return $this->__run('getListData', array($principalType, $user));
    }
    
    /**

     * @return Java type: org.cyclos.model.access.clients.AccessClientsSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO     * @param channel Java type: org.cyclos.model.access.channels.ChannelVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#getTypeData(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.access.channels.ChannelVO)
     */
    public function getTypeData($user, $channel) {
        return $this->__run('getTypeData', array($user, $channel));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.access.clients.AccessClientLocatorVO
     * @return Java type: org.cyclos.model.access.clients.AccessClientVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#locate(org.cyclos.model.access.clients.AccessClientLocatorVO)
     */
    public function locate($locator) {
        return $this->__run('locate', array($locator));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param query Java type: org.cyclos.model.access.clients.AccessClientQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#search(org.cyclos.model.access.clients.AccessClientQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.clients.AccessClientActionParams
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#unassign(org.cyclos.model.access.clients.AccessClientActionParams)
     */
    public function unassign($params) {
        $this->__run('unassign', array($params));
    }
    
    /**
     * @param action Java type: org.cyclos.model.ActionWithConfirmationPassword
     * @return Java type: boolean
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#unassignCurrent(org.cyclos.model.ActionWithConfirmationPassword)
     */
    public function unassignCurrent($action) {
        return $this->__run('unassignCurrent', array($action));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.clients.AccessClientActionParams
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/AccessClientService.html#unblock(org.cyclos.model.access.clients.AccessClientActionParams)
     */
    public function unblock($params) {
        $this->__run('unblock', array($params));
    }
    
}

?>
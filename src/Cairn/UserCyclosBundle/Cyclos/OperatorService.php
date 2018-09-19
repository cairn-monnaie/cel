<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class OperatorService extends Service {

    function __construct() {
        parent::__construct('operatorService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.operators.OperatorsSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#getSearchData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getSearchData($user) {
        return $this->__run('getSearchData', array($user));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.operators.ViewOperatorProfileData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#getViewProfileData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getViewProfileData($locator) {
        return $this->__run('getViewProfileData', array($locator));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.users.operators.OperatorRegistrationDTO
     * @return Java type: org.cyclos.model.users.users.UserRegistrationResult
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#register(org.cyclos.model.users.operators.OperatorRegistrationDTO)
     */
    public function register($dto) {
        return $this->__run('register', array($dto));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param params Java type: org.cyclos.model.users.operators.OperatorQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/OperatorService.html#search(org.cyclos.model.users.operators.OperatorQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
}

?>
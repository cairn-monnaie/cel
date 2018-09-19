<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ConnectedUserService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ConnectedUserService extends Service {

    function __construct() {
        parent::__construct('connectedUserService');
    }
    
    /**
     * @param userLocator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: int
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ConnectedUserService.html#disconnect(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function disconnect($userLocator) {
        return $this->__run('disconnect', array($userLocator));
    }
    
    /**
     * @param sessionTokens Java type: java.util.Set
     * @return Java type: int
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ConnectedUserService.html#disconnectBySessionTokens(java.util.Set)
     */
    public function disconnectBySessionTokens($sessionTokens) {
        return $this->__run('disconnectBySessionTokens', array($sessionTokens));
    }
    
    /**

     * @return Java type: org.cyclos.model.users.users.ConnectedUserSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ConnectedUserService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param query Java type: org.cyclos.model.users.users.ConnectedUserQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/ConnectedUserService.html#search(org.cyclos.model.users.users.ConnectedUserQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
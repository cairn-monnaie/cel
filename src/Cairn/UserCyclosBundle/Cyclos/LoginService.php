<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class LoginService extends Service {

    function __construct() {
        parent::__construct('loginService');
    }
    
    /**
     * @param password Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#checkSecondaryPassword(java.lang.String)
     */
    public function checkSecondaryPassword($password) {
        $this->__run('checkSecondaryPassword', array($password));
    }
    
    /**
     * @param channelName Java type: java.lang.String
     * @return Java type: org.cyclos.model.access.LoginData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#getLoginData(java.lang.String)
     */
    public function getLoginData($channelName) {
        return $this->__run('getLoginData', array($channelName));
    }
    
    /**

     * @return Java type: org.cyclos.model.access.passwords.PasswordInputDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#getSecondaryPasswordInput()
     */
    public function getSecondaryPasswordInput() {
        return $this->__run('getSecondaryPasswordInput', array());
    }
    
    /**
     * @param sessionTimeout Java type: org.cyclos.model.utils.TimeIntervalDTO
     * @return Java type: org.cyclos.model.users.users.UserLoginResult
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#login(org.cyclos.model.utils.TimeIntervalDTO)
     */
    public function login($sessionTimeout) {
        return $this->__run('login', array($sessionTimeout));
    }
    
    /**
     * @param params Java type: org.cyclos.model.users.users.UserLoginDTO
     * @return Java type: org.cyclos.model.users.users.UserLoginDetailedResult
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#loginUser(org.cyclos.model.users.users.UserLoginDTO)
     */
    public function loginUser($params) {
        return $this->__run('loginUser', array($params));
    }
    
    /**

     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#logout()
     */
    public function logout() {
        $this->__run('logout', array());
    }
    
    /**
     * @param token Java type: java.lang.String
     * @return Java type: boolean
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#logoutUser(java.lang.String)
     */
    public function logoutUser($token) {
        return $this->__run('logoutUser', array($token));
    }
    
    /**

     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/LoginService.html#replaceSession()
     */
    public function replaceSession() {
        return $this->__run('replaceSession', array());
    }
    
}

?>
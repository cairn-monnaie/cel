<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class PasswordService extends Service {

    function __construct() {
        parent::__construct('passwordService');
    }
    
    /**
     * @param passwordType Java type: org.cyclos.model.access.passwordtypes.PasswordTypeVO
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#activate(org.cyclos.model.access.passwordtypes.PasswordTypeVO)
     */
    public function activate($passwordType) {
        return $this->__run('activate', array($passwordType));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.PasswordActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#allowActivation(org.cyclos.model.access.passwords.PasswordActionDTO)
     */
    public function allowActivation($params) {
        $this->__run('allowActivation', array($params));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.passwords.ChangePasswordDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#change(org.cyclos.model.access.passwords.ChangePasswordDTO)
     */
    public function change($dto) {
        $this->__run('change', array($dto));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.passwords.ChangeForgottenPasswordDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#changeForgottenPassword(org.cyclos.model.access.passwords.ChangeForgottenPasswordDTO)
     */
    public function changeForgottenPassword($dto) {
        $this->__run('changeForgottenPassword', array($dto));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.PasswordActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#disable(org.cyclos.model.access.passwords.PasswordActionDTO)
     */
    public function disable($params) {
        $this->__run('disable', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.PasswordActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#enable(org.cyclos.model.access.passwords.PasswordActionDTO)
     */
    public function enable($params) {
        $this->__run('enable', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.ForgotPasswordRequestDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#forgotPasswordRequest(org.cyclos.model.access.passwords.ForgotPasswordRequestDTO)
     */
    public function forgotPasswordRequest($params) {
        $this->__run('forgotPasswordRequest', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.ChangeGeneratedPasswordDTO
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#generateNew(org.cyclos.model.access.passwords.ChangeGeneratedPasswordDTO)
     */
    public function generateNew($params) {
        return $this->__run('generateNew', array($params));
    }
    
    /**
     * @param validationKey Java type: java.lang.String
     * @return Java type: org.cyclos.model.access.passwords.ChangeForgottenPasswordData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#getChangeForgottenPasswordData(java.lang.String)
     */
    public function getChangeForgottenPasswordData($validationKey) {
        return $this->__run('getChangeForgottenPasswordData', array($validationKey));
    }
    
    /**
     * @param changeSecondaryPassword Java type: boolean
     * @return Java type: org.cyclos.model.access.passwords.ChangePasswordData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#getChangePasswordData(boolean)
     */
    public function getChangePasswordData($changeSecondaryPassword) {
        return $this->__run('getChangePasswordData', array($changeSecondaryPassword));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.access.passwords.UserPasswordsData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#getData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getData($locator) {
        return $this->__run('getData', array($locator));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param type Java type: org.cyclos.model.access.passwordtypes.PasswordTypeVO
     * @return Java type: org.cyclos.model.access.passwords.PasswordData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#getPasswordData(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.access.passwordtypes.PasswordTypeVO)
     */
    public function getPasswordData($locator, $type) {
        return $this->__run('getPasswordData', array($locator, $type));
    }
    
    /**

     * @return Java type: org.cyclos.model.access.passwords.SetSecurityQuestionData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#getSetSecurityQuestionData()
     */
    public function getSetSecurityQuestionData() {
        return $this->__run('getSetSecurityQuestionData', array());
    }
    
    /**
     * @param medium Java type: org.cyclos.model.utils.SendMedium
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#requestNewOTP(org.cyclos.model.utils.SendMedium)
     */
    public function requestNewOTP($medium) {
        return $this->__run('requestNewOTP', array($medium));
    }
    
    /**
     * @param medium Java type: org.cyclos.model.utils.SendMedium
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#requestNewOTPForSecondaryPassword(org.cyclos.model.utils.SendMedium)
     */
    public function requestNewOTPForSecondaryPassword($medium) {
        return $this->__run('requestNewOTPForSecondaryPassword', array($medium));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.PasswordActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#reset(org.cyclos.model.access.passwords.PasswordActionDTO)
     */
    public function reset($params) {
        $this->__run('reset', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.ResetAndSendPasswordDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#resetAndSend(org.cyclos.model.access.passwords.ResetAndSendPasswordDTO)
     */
    public function resetAndSend($params) {
        $this->__run('resetAndSend', array($params));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#resetSecurityQuestion(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function resetSecurityQuestion($locator) {
        $this->__run('resetSecurityQuestion', array($locator));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.SetSecurityQuestionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#setSecurityQuestion(org.cyclos.model.access.passwords.SetSecurityQuestionDTO)
     */
    public function setSecurityQuestion($params) {
        $this->__run('setSecurityQuestion', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.passwords.PasswordActionDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/access/PasswordService.html#unblock(org.cyclos.model.access.passwords.PasswordActionDTO)
     */
    public function unblock($params) {
        $this->__run('unblock', array($params));
    }
    
}

?>
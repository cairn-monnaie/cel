<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class PhoneService extends Service {

    function __construct() {
        parent::__construct('phoneService');
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param confirmationPassword Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#disableForSms(java.lang.Long,%20java.lang.String)
     */
    public function disableForSms($id, $confirmationPassword) {
        $this->__run('disableForSms', array($id, $confirmationPassword));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#enableForSms(java.lang.Long)
     */
    public function enableForSms($id) {
        $this->__run('enableForSms', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.access.passwords.PasswordInputDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#getConfirmationPasswordInputForDisableSms(java.lang.Long)
     */
    public function getConfirmationPasswordInputForDisableSms($id) {
        return $this->__run('getConfirmationPasswordInputForDisableSms', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.access.passwords.PasswordInputDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#getConfirmationPasswordInputForRemove(java.lang.Long)
     */
    public function getConfirmationPasswordInputForRemove($id) {
        return $this->__run('getConfirmationPasswordInputForRemove', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.phones.PhoneListData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#getPhoneListData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getPhoneListData($locator) {
        return $this->__run('getPhoneListData', array($locator));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param number Java type: java.lang.String
     * @return Java type: org.cyclos.model.users.phones.PhoneVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#loadByNumber(java.lang.String)
     */
    public function loadByNumber($number) {
        return $this->__run('loadByNumber', array($number));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param ids Java type: java.util.Collection     * @param confirmationPassword Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#removeAllWithConfirmationPassword(java.util.Collection,%20java.lang.String)
     */
    public function removeAllWithConfirmationPassword($ids, $confirmationPassword) {
        $this->__run('removeAllWithConfirmationPassword', array($ids, $confirmationPassword));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param confirmationPassword Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#removeWithConfirmationPassword(java.lang.Long,%20java.lang.String)
     */
    public function removeWithConfirmationPassword($id, $confirmationPassword) {
        $this->__run('removeWithConfirmationPassword', array($id, $confirmationPassword));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param object Java type: DTO     * @param confirmationPassword Java type: java.lang.String
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#saveWithConfirmationPassword(DTO,%20java.lang.String)
     */
    public function saveWithConfirmationPassword($object, $confirmationPassword) {
        return $this->__run('saveWithConfirmationPassword', array($object, $confirmationPassword));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#sendVerificationCode(java.lang.Long)
     */
    public function sendVerificationCode($id) {
        return $this->__run('sendVerificationCode', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param verificationCode Java type: java.lang.String
     * @return Java type: org.cyclos.model.users.phones.CodeVerificationStatus
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/PhoneService.html#verify(java.lang.Long,%20java.lang.String)
     */
    public function verify($id, $verificationCode) {
        return $this->__run('verify', array($id, $verificationCode));
    }
    
}

?>
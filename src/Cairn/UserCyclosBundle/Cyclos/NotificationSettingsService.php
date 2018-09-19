<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/NotificationSettingsService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class NotificationSettingsService extends Service {

    function __construct() {
        parent::__construct('notificationSettingsService');
    }
    
    /**

     * @return Java type: org.cyclos.model.messaging.notificationsettings.AdminNotificationSettingsData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/NotificationSettingsService.html#getAdminNotificationSettingsData()
     */
    public function getAdminNotificationSettingsData() {
        return $this->__run('getAdminNotificationSettingsData', array());
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.messaging.notificationsettings.UserNotificationSettingsData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/NotificationSettingsService.html#getUserNotificationSettingsData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getUserNotificationSettingsData($locator) {
        return $this->__run('getUserNotificationSettingsData', array($locator));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.messaging.notificationsettings.AdminNotificationSettingsDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/NotificationSettingsService.html#saveAdmin(org.cyclos.model.messaging.notificationsettings.AdminNotificationSettingsDTO)
     */
    public function saveAdmin($dto) {
        $this->__run('saveAdmin', array($dto));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.messaging.notificationsettings.UserNotificationSettingsDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/NotificationSettingsService.html#saveUser(org.cyclos.model.messaging.notificationsettings.UserNotificationSettingsDTO)
     */
    public function saveUser($dto) {
        $this->__run('saveUser', array($dto));
    }
    
}

?>
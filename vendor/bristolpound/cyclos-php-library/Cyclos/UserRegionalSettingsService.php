<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserRegionalSettingsService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class UserRegionalSettingsService extends Service {

    function __construct() {
        parent::__construct('userRegionalSettingsService');
    }
    
    /**

     * @return Java type: org.cyclos.model.users.users.RegionalSettingsDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserRegionalSettingsService.html#get()
     */
    public function get() {
        return $this->__run('get', array());
    }
    
    /**
     * @param settings Java type: org.cyclos.model.users.users.RegionalSettingsDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserRegionalSettingsService.html#save(org.cyclos.model.users.users.RegionalSettingsDTO)
     */
    public function save($settings) {
        $this->__run('save', array($settings));
    }
    
}

?>
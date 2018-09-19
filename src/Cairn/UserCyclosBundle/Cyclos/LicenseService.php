<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/LicenseService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class LicenseService extends Service {

    function __construct() {
        parent::__construct('licenseService');
    }
    
    /**

     * @return Java type: org.cyclos.model.system.licensing.BasicLicenseVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/LicenseService.html#getBasicLicense()
     */
    public function getBasicLicense() {
        return $this->__run('getBasicLicense', array());
    }
    
    /**

     * @return Java type: org.cyclos.model.system.licensing.LicenseVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/LicenseService.html#getLicense()
     */
    public function getLicense() {
        return $this->__run('getLicense', array());
    }
    
    /**
     * @param in Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/LicenseService.html#offlineUpdate(org.cyclos.server.utils.SerializableInputStream)
     */
    public function offlineUpdate($in) {
        $this->__run('offlineUpdate', array($in));
    }
    
    /**

     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/LicenseService.html#onlineUpdate()
     */
    public function onlineUpdate() {
        $this->__run('onlineUpdate', array());
    }
    
}

?>
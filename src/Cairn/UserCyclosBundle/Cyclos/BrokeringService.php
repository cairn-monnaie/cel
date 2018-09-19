<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class BrokeringService extends Service {

    function __construct() {
        parent::__construct('brokeringService');
    }
    
    /**
     * @param userLocator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param brokerLocator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param mainBroker Java type: boolean
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#addBroker(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.users.users.UserLocatorVO,%20boolean)
     */
    public function addBroker($userLocator, $brokerLocator, $mainBroker) {
        return $this->__run('addBroker', array($userLocator, $brokerLocator, $mainBroker));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.brokering.AddBrokerData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#getAddBrokerData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getAddBrokerData($locator) {
        return $this->__run('getAddBrokerData', array($locator));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#getBrokeringLogs(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getBrokeringLogs($locator) {
        return $this->__run('getBrokeringLogs', array($locator));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.brokering.BrokeringData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#getData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getData($locator) {
        return $this->__run('getData', array($locator));
    }
    
    /**
     * @param userLocator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param brokerLocator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#removeBroker(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function removeBroker($userLocator, $brokerLocator) {
        $this->__run('removeBroker', array($userLocator, $brokerLocator));
    }
    
    /**
     * @param userLocator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param brokerLocator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/BrokeringService.html#setMainBroker(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function setMainBroker($userLocator, $brokerLocator) {
        $this->__run('setMainBroker', array($userLocator, $brokerLocator));
    }
    
}

?>
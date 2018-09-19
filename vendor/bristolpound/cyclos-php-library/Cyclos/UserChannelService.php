<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/UserChannelService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class UserChannelService extends Service {

    function __construct() {
        parent::__construct('userChannelService');
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.access.userchannels.UserChannelsData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/UserChannelService.html#getChannelsData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getChannelsData($locator) {
        return $this->__run('getChannelsData', array($locator));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param channels Java type: java.util.Set
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/UserChannelService.html#saveChannels(org.cyclos.model.users.users.UserLocatorVO,%20java.util.Set)
     */
    public function saveChannels($locator, $channels) {
        $this->__run('saveChannels', array($locator, $channels));
    }
    
}

?>
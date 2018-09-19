<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/SmsService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class SmsService extends Service {

    function __construct() {
        parent::__construct('smsService');
    }
    
    /**
     * @param smsMessageId Java type: java.lang.Long
     * @return Java type: org.cyclos.model.messaging.messages.InboundSmsMessageVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/SmsService.html#getInboundMessageData(java.lang.Long)
     */
    public function getInboundMessageData($smsMessageId) {
        return $this->__run('getInboundMessageData', array($smsMessageId));
    }
    
    /**
     * @param smsMessageId Java type: java.lang.Long
     * @return Java type: org.cyclos.model.messaging.messages.OutboundSmsMessageVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/SmsService.html#getOutboundMessageData(java.lang.Long)
     */
    public function getOutboundMessageData($smsMessageId) {
        return $this->__run('getOutboundMessageData', array($smsMessageId));
    }
    
    /**
     * @param query Java type: org.cyclos.model.messaging.messages.InboundSmsQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/SmsService.html#searchInboundMessages(org.cyclos.model.messaging.messages.InboundSmsQuery)
     */
    public function searchInboundMessages($query) {
        return $this->__run('searchInboundMessages', array($query));
    }
    
    /**
     * @param query Java type: org.cyclos.model.messaging.messages.OutboundSmsQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/SmsService.html#searchOutboundMessages(org.cyclos.model.messaging.messages.OutboundSmsQuery)
     */
    public function searchOutboundMessages($query) {
        return $this->__run('searchOutboundMessages', array($query));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class MailingListService extends Service {

    function __construct() {
        parent::__construct('mailingListService');
    }
    
    /**

     * @return Java type: org.cyclos.model.messaging.mailinglists.MailingListSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param type Java type: org.cyclos.model.messaging.mailinglists.MailingListType
     * @return Java type: org.cyclos.model.messaging.mailinglists.SendMailingListData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html#getSendData(org.cyclos.model.messaging.mailinglists.MailingListType)
     */
    public function getSendData($type) {
        return $this->__run('getSendData', array($type));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.messaging.mailinglists.MailingListDetailedVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param query Java type: org.cyclos.model.messaging.mailinglists.MailingListQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html#search(org.cyclos.model.messaging.mailinglists.MailingListQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param object Java type: org.cyclos.model.messaging.mailinglists.SendMailingListDTO
     * @return Java type: int
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/messaging/MailingListService.html#send(org.cyclos.model.messaging.mailinglists.SendMailingListDTO)
     */
    public function send($object) {
        return $this->__run('send', array($object));
    }
    
}

?>
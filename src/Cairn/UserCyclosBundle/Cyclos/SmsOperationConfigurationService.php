<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class SmsOperationConfigurationService extends Service {

    function __construct() {
        parent::__construct('smsOperationConfigurationService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param smsChannelConfigurationId Java type: java.lang.Long
     * @return Java type: org.cyclos.model.system.smsoperationconfigurations.SmsTextsListData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#getSmsTextsListData(java.lang.Long)
     */
    public function getSmsTextsListData($smsChannelConfigurationId) {
        return $this->__run('getSmsTextsListData', array($smsChannelConfigurationId));
    }
    
    /**
     * @param smsChannelConfigurationId Java type: java.lang.Long
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#listSmsTexts(java.lang.Long)
     */
    public function listSmsTexts($smsChannelConfigurationId) {
        return $this->__run('listSmsTexts', array($smsChannelConfigurationId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/SmsOperationConfigurationService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
}

?>
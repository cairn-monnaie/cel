<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class NetworkService extends Service {

    function __construct() {
        parent::__construct('networkService');
    }
    
    /**
     * @param network Java type: org.cyclos.model.system.networks.NetworkDTO     * @param data Java type: org.cyclos.model.system.networks.NetworkInitialDataDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#createWithData(org.cyclos.model.system.networks.NetworkDTO,%20org.cyclos.model.system.networks.NetworkInitialDataDTO)
     */
    public function createWithData($network, $data) {
        return $this->__run('createWithData', array($network, $data));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**

     * @return Java type: org.cyclos.model.system.networks.NetworkWithInitialDataData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#getDataForNewWithData()
     */
    public function getDataForNewWithData() {
        return $this->__run('getDataForNewWithData', array());
    }
    
    /**
     * @param basicData Java type: org.cyclos.model.system.networks.BasicNetworkInitialDataDTO
     * @return Java type: org.cyclos.model.system.networks.NetworkInitialDataDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#getInitialData(org.cyclos.model.system.networks.BasicNetworkInitialDataDTO)
     */
    public function getInitialData($basicData) {
        return $this->__run('getInitialData', array($basicData));
    }
    
    /**

     * @return Java type: org.cyclos.model.system.networks.NetworkSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param network Java type: org.cyclos.model.system.networks.NetworkVO
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#resolveNetworkUrl(org.cyclos.model.system.networks.NetworkVO)
     */
    public function resolveNetworkUrl($network) {
        return $this->__run('resolveNetworkUrl', array($network));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param query Java type: org.cyclos.model.system.networks.NetworkQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/NetworkService.html#search(org.cyclos.model.system.networks.NetworkQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
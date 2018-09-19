<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AdService extends Service {

    function __construct() {
        parent::__construct('productsAndServices');
    }
    
    /**
     * @param type Java type: org.cyclos.model.marketplace.advertisements.AdType     * @param overBrokeredUsers Java type: boolean
     * @return Java type: org.cyclos.model.marketplace.advertisements.AdSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#getAdSearchData(org.cyclos.model.marketplace.advertisements.AdType,%20boolean)
     */
    public function getAdSearchData($type, $overBrokeredUsers) {
        return $this->__run('getAdSearchData', array($type, $overBrokeredUsers));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param type Java type: org.cyclos.model.marketplace.advertisements.AdType
     * @return Java type: org.cyclos.model.marketplace.advertisements.UserAdsSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#getUserAdsSearchData(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.marketplace.advertisements.AdType)
     */
    public function getUserAdsSearchData($locator, $type) {
        return $this->__run('getUserAdsSearchData', array($locator, $type));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.marketplace.advertisements.AdViewData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#getViewData(java.lang.Long)
     */
    public function getViewData($id) {
        return $this->__run('getViewData', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.marketplace.advertisements.BasicAdVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#loadVO(java.lang.Long)
     */
    public function loadVO($id) {
        return $this->__run('loadVO', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param params Java type: org.cyclos.model.marketplace.advertisements.BasicAdQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#search(org.cyclos.model.marketplace.advertisements.BasicAdQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param comments Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#setAsDraft(java.lang.Long,%20java.lang.String)
     */
    public function setAsDraft($id, $comments) {
        $this->__run('setAsDraft', array($id, $comments));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param hidden Java type: boolean
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/marketplace/AdService.html#setHidden(java.lang.Long,%20boolean)
     */
    public function setHidden($id, $hidden) {
        $this->__run('setHidden', array($id, $hidden));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class AdCategoryService extends Service {

    function __construct() {
        parent::__construct('adCategoryService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param categories Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#saveCategories(java.util.List)
     */
    public function saveCategories($categories) {
        $this->__run('saveCategories', array($categories));
    }
    
    /**
     * @param categoryIds Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#saveOrder(java.util.List)
     */
    public function saveOrder($categoryIds) {
        $this->__run('saveOrder', array($categoryIds));
    }
    
    /**
     * @param params Java type: org.cyclos.model.marketplace.categories.AdCategoryQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#search(org.cyclos.model.marketplace.categories.AdCategoryQuery)
     */
    public function search($params) {
        return $this->__run('search', array($params));
    }
    
    /**
     * @param adCategory Java type: org.cyclos.model.marketplace.categories.AdCategoryVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/marketplace/AdCategoryService.html#sortAlphabetically(org.cyclos.model.marketplace.categories.AdCategoryVO)
     */
    public function sortAlphabetically($adCategory) {
        $this->__run('sortAlphabetically', array($adCategory));
    }
    
}

?>
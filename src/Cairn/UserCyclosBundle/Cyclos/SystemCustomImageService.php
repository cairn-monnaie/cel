<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class SystemCustomImageService extends Service {

    function __construct() {
        parent::__construct('systemCustomImageService');
    }
    
    /**

     * @return Java type: org.cyclos.model.system.images.AccessibleSystemImageCategoriesData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#getAccessibleImageCategories()
     */
    public function getAccessibleImageCategories() {
        return $this->__run('getAccessibleImageCategories', array());
    }
    
    /**

     * @return Java type: org.cyclos.model.system.images.SystemCustomImagesSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#getSearchData()
     */
    public function getSearchData() {
        return $this->__run('getSearchData', array());
    }
    
    /**
     * @param categoryId Java type: java.lang.Long
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#list(java.lang.Long)
     */
    public function _list($categoryId) {
        return $this->__run('list', array($categoryId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param key Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#loadByKey(java.lang.String)
     */
    public function loadByKey($key) {
        return $this->__run('loadByKey', array($key));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param param Java type: NP     * @param name Java type: java.lang.String     * @param contents Java type: org.cyclos.server.utils.SerializableInputStream     * @param contentType Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#save(NP,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream,%20java.lang.String)
     */
    public function save($param, $name, $contents, $contentType) {
        return $this->__run('save', array($param, $name, $contents, $contentType));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param systemImageCategory Java type: org.cyclos.model.contentmanagement.imagecategories.SystemImageCategoryVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#saveCategory(java.lang.Long,%20org.cyclos.model.contentmanagement.imagecategories.SystemImageCategoryVO)
     */
    public function saveCategory($id, $systemImageCategory) {
        $this->__run('saveCategory', array($id, $systemImageCategory));
    }
    
    /**
     * @param image Java type: org.cyclos.model.system.images.SystemCustomImageDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#saveDetails(org.cyclos.model.system.images.SystemCustomImageDTO)
     */
    public function saveDetails($image) {
        return $this->__run('saveDetails', array($image));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param name Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/SystemCustomImageService.html#saveName(java.lang.Long,%20java.lang.String)
     */
    public function saveName($id, $name) {
        $this->__run('saveName', array($id, $name));
    }
    
}

?>
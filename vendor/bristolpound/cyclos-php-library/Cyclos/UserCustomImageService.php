<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class UserCustomImageService extends Service {

    function __construct() {
        parent::__construct('userCustomImageService');
    }
    
    /**
     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.users.UserCustomImagesListData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#getListData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getListData($user) {
        return $this->__run('getListData', array($user));
    }
    
    /**
     * @param categoryId Java type: java.lang.Long
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#list(java.lang.Long)
     */
    public function _list($categoryId) {
        return $this->__run('list', array($categoryId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param key Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#loadByKey(java.lang.String)
     */
    public function loadByKey($key) {
        return $this->__run('loadByKey', array($key));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param param Java type: NP     * @param name Java type: java.lang.String     * @param contents Java type: org.cyclos.server.utils.SerializableInputStream     * @param contentType Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#save(NP,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream,%20java.lang.String)
     */
    public function save($param, $name, $contents, $contentType) {
        return $this->__run('save', array($param, $name, $contents, $contentType));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param name Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/UserCustomImageService.html#saveName(java.lang.Long,%20java.lang.String)
     */
    public function saveName($id, $name) {
        $this->__run('saveName', array($id, $name));
    }
    
}

?>
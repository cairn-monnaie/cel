<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class UserImageService extends Service {

    function __construct() {
        parent::__construct('userImageService');
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.users.UserImageVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#getFirst(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getFirst($locator) {
        return $this->__run('getFirst', array($locator));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.users.UserImagesListData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#getListData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getListData($locator) {
        return $this->__run('getListData', array($locator));
    }
    
    /**
     * @param ownerId Java type: java.lang.Long
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#list(java.lang.Long)
     */
    public function _list($ownerId) {
        return $this->__run('list', array($ownerId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param key Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#loadByKey(java.lang.String)
     */
    public function loadByKey($key) {
        return $this->__run('loadByKey', array($key));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param param Java type: NP     * @param name Java type: java.lang.String     * @param contents Java type: org.cyclos.server.utils.SerializableInputStream     * @param contentType Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#save(NP,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream,%20java.lang.String)
     */
    public function save($param, $name, $contents, $contentType) {
        return $this->__run('save', array($param, $name, $contents, $contentType));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param name Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#saveName(java.lang.Long,%20java.lang.String)
     */
    public function saveName($id, $name) {
        $this->__run('saveName', array($id, $name));
    }
    
    /**
     * @param ownerId Java type: java.lang.Long     * @param imageIds Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/users/UserImageService.html#saveOrder(java.lang.Long,%20java.util.List)
     */
    public function saveOrder($ownerId, $imageIds) {
        $this->__run('saveOrder', array($ownerId, $imageIds));
    }
    
}

?>
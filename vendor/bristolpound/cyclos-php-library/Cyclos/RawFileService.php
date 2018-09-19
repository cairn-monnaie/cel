<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class RawFileService extends Service {

    function __construct() {
        parent::__construct('rawFileService');
    }
    
    /**
     * @param guestKey Java type: java.lang.String
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#listTemp(java.lang.String)
     */
    public function listTemp($guestKey) {
        return $this->__run('listTemp', array($guestKey));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.utils.RawFileVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#readContent(java.lang.Long)
     */
    public function readContent($id) {
        return $this->__run('readContent', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#removeAll(java.util.List)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param name Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#saveName(java.lang.Long,%20java.lang.String)
     */
    public function saveName($id, $name) {
        $this->__run('saveName', array($id, $name));
    }
    
    /**
     * @param guestKey Java type: java.lang.String     * @param name Java type: java.lang.String     * @param contentType Java type: java.lang.String     * @param content Java type: org.cyclos.server.utils.SerializableInputStream
     * @return Java type: org.cyclos.model.utils.RawFileVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/system/RawFileService.html#saveTemp(java.lang.String,%20java.lang.String,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream)
     */
    public function saveTemp($guestKey, $name, $contentType, $content) {
        return $this->__run('saveTemp', array($guestKey, $name, $contentType, $content));
    }
    
}

?>
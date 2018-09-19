<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class VoucherTypeImageService extends Service {

    function __construct() {
        parent::__construct('voucherTypeImageService');
    }
    
    /**
     * @param ownerId Java type: java.lang.Long
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#list(java.lang.Long)
     */
    public function _list($ownerId) {
        return $this->__run('list', array($ownerId));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param key Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#loadByKey(java.lang.String)
     */
    public function loadByKey($key) {
        return $this->__run('loadByKey', array($key));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param param Java type: NP     * @param name Java type: java.lang.String     * @param contents Java type: org.cyclos.server.utils.SerializableInputStream     * @param contentType Java type: java.lang.String
     * @return Java type: VO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#save(NP,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream,%20java.lang.String)
     */
    public function save($param, $name, $contents, $contentType) {
        return $this->__run('save', array($param, $name, $contents, $contentType));
    }
    
    /**
     * @param id Java type: java.lang.Long     * @param name Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#saveName(java.lang.Long,%20java.lang.String)
     */
    public function saveName($id, $name) {
        $this->__run('saveName', array($id, $name));
    }
    
    /**
     * @param ownerId Java type: java.lang.Long     * @param imageIds Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/banking/VoucherTypeImageService.html#saveOrder(java.lang.Long,%20java.util.List)
     */
    public function saveOrder($ownerId, $imageIds) {
        $this->__run('saveOrder', array($ownerId, $imageIds));
    }
    
}

?>
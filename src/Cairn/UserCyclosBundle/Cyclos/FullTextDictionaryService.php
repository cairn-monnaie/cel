<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/FullTextDictionaryService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class FullTextDictionaryService extends Service {

    function __construct() {
        parent::__construct('fullTextDictionaryService');
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/system/FullTextDictionaryService.html#list()
     */
    public function _list() {
        return $this->__run('list', array());
    }
    
}

?>
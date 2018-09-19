<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class DocumentService extends Service {

    function __construct() {
        parent::__construct('documentService');
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param document Java type: org.cyclos.model.contentmanagement.documents.DocumentVO
     * @return Java type: org.cyclos.model.contentmanagement.documents.DocumentVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getDocument(org.cyclos.model.contentmanagement.documents.DocumentVO)
     */
    public function getDocument($document) {
        return $this->__run('getDocument', array($document));
    }
    
    /**
     * @param document Java type: org.cyclos.model.contentmanagement.documents.DocumentVO
     * @return Java type: org.cyclos.model.utils.RawFileVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getDocumentFile(org.cyclos.model.contentmanagement.documents.DocumentVO)
     */
    public function getDocumentFile($document) {
        return $this->__run('getDocumentFile', array($document));
    }
    
    /**
     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.contentmanagement.documents.DocumentSearchData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getDocumentSearchData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getDocumentSearchData($user) {
        return $this->__run('getDocumentSearchData', array($user));
    }
    
    /**
     * @param document Java type: org.cyclos.model.contentmanagement.documents.DocumentVO     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.contentmanagement.documents.ProcessDynamicDocumentData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#getProcessDynamicDocumentData(org.cyclos.model.contentmanagement.documents.DocumentVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getProcessDynamicDocumentData($document, $locator) {
        return $this->__run('getProcessDynamicDocumentData', array($document, $locator));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#listMyDocuments()
     */
    public function listMyDocuments() {
        return $this->__run('listMyDocuments', array());
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param params Java type: org.cyclos.model.contentmanagement.documents.ProcessDynamicDocumentDTO
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#processDynamicDocument(org.cyclos.model.contentmanagement.documents.ProcessDynamicDocumentDTO)
     */
    public function processDynamicDocument($params) {
        return $this->__run('processDynamicDocument', array($params));
    }
    
    /**
     * @param document Java type: org.cyclos.model.contentmanagement.documents.DocumentVO
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#readContent(org.cyclos.model.contentmanagement.documents.DocumentVO)
     */
    public function readContent($document) {
        return $this->__run('readContent', array($document));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param document Java type: org.cyclos.model.contentmanagement.documents.DocumentVO     * @param contentType Java type: java.lang.String     * @param fileName Java type: java.lang.String     * @param contents Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#saveFile(org.cyclos.model.contentmanagement.documents.DocumentVO,%20java.lang.String,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream)
     */
    public function saveFile($document, $contentType, $fileName, $contents) {
        $this->__run('saveFile', array($document, $contentType, $fileName, $contents));
    }
    
    /**
     * @param query Java type: org.cyclos.model.contentmanagement.documents.DocumentQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/DocumentService.html#search(org.cyclos.model.contentmanagement.documents.DocumentQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
}

?>
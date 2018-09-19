<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class TokenService extends Service {

    function __construct() {
        parent::__construct('tokenService');
    }
    
    /**
     * @param tokenId Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#activatePending(java.lang.Long)
     */
    public function activatePending($tokenId) {
        $this->__run('activatePending', array($tokenId));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.tokens.TokenActionDTO     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#assign(org.cyclos.model.access.tokens.TokenActionDTO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function assign($dto, $locator) {
        $this->__run('assign', array($dto, $locator));
    }
    
    /**
     * @param params Java type: org.cyclos.model.access.tokens.TokenBarcodeParams
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#barcode(org.cyclos.model.access.tokens.TokenBarcodeParams)
     */
    public function barcode($params) {
        return $this->__run('barcode', array($params));
    }
    
    /**
     * @param tokenId Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#block(java.lang.Long)
     */
    public function block($tokenId) {
        $this->__run('block', array($tokenId));
    }
    
    /**
     * @param tokenId Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#cancel(java.lang.Long)
     */
    public function cancel($tokenId) {
        $this->__run('cancel', array($tokenId));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.tokens.TokenActionDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#cancelNFCToken(org.cyclos.model.access.tokens.TokenActionDTO)
     */
    public function cancelNFCToken($dto) {
        $this->__run('cancelNFCToken', array($dto));
    }
    
    /**
     * @param query Java type: org.cyclos.model.access.tokens.TokenQuery
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#exportToCSV(org.cyclos.model.access.tokens.TokenQuery)
     */
    public function exportToCSV($query) {
        return $this->__run('exportToCSV', array($query));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**

     * @return Java type: org.cyclos.model.access.tokens.InitializeNFCTagData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getInitializeNFCTagData()
     */
    public function getInitializeNFCTagData() {
        return $this->__run('getInitializeNFCTagData', array());
    }
    
    /**
     * @param tokenType Java type: org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO     * @param user Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.access.tokens.TokensListData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getListData(org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getListData($tokenType, $user) {
        return $this->__run('getListData', array($tokenType, $user));
    }
    
    /**
     * @param tokenType Java type: org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.access.tokens.PersonalizeNFCTagData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getPersonalizeNFCTagData(org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO,%20org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getPersonalizeNFCTagData($tokenType, $locator) {
        return $this->__run('getPersonalizeNFCTagData', array($tokenType, $locator));
    }
    
    /**
     * @param tokenType Java type: org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO
     * @return Java type: org.cyclos.model.access.tokens.TokenSearchData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#getSearchData(org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO)
     */
    public function getSearchData($tokenType) {
        return $this->__run('getSearchData', array($tokenType));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.tokens.NFCTagInitializeDTO
     * @return Java type: org.cyclos.model.access.tokens.InitializeNFCTagResult
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#initializeNFCTag(org.cyclos.model.access.tokens.NFCTagInitializeDTO)
     */
    public function initializeNFCTag($dto) {
        return $this->__run('initializeNFCTag', array($dto));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.tokens.NFCTagPersonalizeDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#personalizeNFCTag(org.cyclos.model.access.tokens.NFCTagPersonalizeDTO)
     */
    public function personalizeNFCTag($dto) {
        $this->__run('personalizeNFCTag', array($dto));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param dto Java type: org.cyclos.model.access.tokens.ExternalNFCTagAuthenticateDTO
     * @return Java type: org.cyclos.model.access.tokens.ExternalNFCTagAuthenticateData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#requestForExternalAuthenticate(org.cyclos.model.access.tokens.ExternalNFCTagAuthenticateDTO)
     */
    public function requestForExternalAuthenticate($dto) {
        return $this->__run('requestForExternalAuthenticate', array($dto));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO     * @param tokenTypeVO Java type: org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO     * @param medium Java type: org.cyclos.model.utils.SendMedium
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#requestNewOTPForPersonalizeNFCTag(org.cyclos.model.users.users.UserLocatorVO,%20org.cyclos.model.access.principaltypes.TokenPrincipalTypeVO,%20org.cyclos.model.utils.SendMedium)
     */
    public function requestNewOTPForPersonalizeNFCTag($locator, $tokenTypeVO, $medium) {
        $this->__run('requestNewOTPForPersonalizeNFCTag', array($locator, $tokenTypeVO, $medium));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
    /**
     * @param query Java type: org.cyclos.model.access.tokens.TokenQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#search(org.cyclos.model.access.tokens.TokenQuery)
     */
    public function search($query) {
        return $this->__run('search', array($query));
    }
    
    /**
     * @param tokenId Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/access/TokenService.html#unblock(java.lang.Long)
     */
    public function unblock($tokenId) {
        $this->__run('unblock', array($tokenId));
    }
    
}

?>
<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html
 * 
 * Generated with Cyclos 4.8.1
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class TranslationMessageService extends Service {

    function __construct() {
        parent::__construct('translationMessageService');
    }
    
    /**
     * @param language Java type: org.cyclos.model.system.languages.LanguageVO
     * @return Java type: org.cyclos.model.contentmanagement.translations.ApplicationTranslationData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#getApplicationTranslationData(org.cyclos.model.system.languages.LanguageVO)
     */
    public function getApplicationTranslationData($language) {
        return $this->__run('getApplicationTranslationData', array($language));
    }
    
    /**
     * @param language Java type: org.cyclos.model.system.languages.LanguageVO
     * @return Java type: java.util.Properties
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#getCustomizedKeysPerLanguage(org.cyclos.model.system.languages.LanguageVO)
     */
    public function getCustomizedKeysPerLanguage($language) {
        return $this->__run('getCustomizedKeysPerLanguage', array($language));
    }
    
    /**
     * @param language Java type: org.cyclos.model.system.languages.LanguageVO     * @param key Java type: java.lang.String
     * @return Java type: org.cyclos.model.contentmanagement.translations.TranslationKeyData
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#getTranslationKeyData(org.cyclos.model.system.languages.LanguageVO,%20java.lang.String)
     */
    public function getTranslationKeyData($language, $key) {
        return $this->__run('getTranslationKeyData', array($language, $key));
    }
    
    /**

     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#listTranslatableLanguages()
     */
    public function listTranslatableLanguages() {
        return $this->__run('listTranslatableLanguages', array());
    }
    
    /**
     * @param language Java type: org.cyclos.model.system.languages.LanguageVO     * @param key Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#restoreOriginalTranslation(org.cyclos.model.system.languages.LanguageVO,%20java.lang.String)
     */
    public function restoreOriginalTranslation($language, $key) {
        $this->__run('restoreOriginalTranslation', array($language, $key));
    }
    
    /**
     * @param language Java type: org.cyclos.model.system.languages.LanguageVO     * @param key Java type: java.lang.String     * @param value Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#saveKey(org.cyclos.model.system.languages.LanguageVO,%20java.lang.String,%20java.lang.String)
     */
    public function saveKey($language, $key, $value) {
        $this->__run('saveKey', array($language, $key, $value));
    }
    
    /**
     * @param params Java type: org.cyclos.model.contentmanagement.translations.TranslationQuery
     * @return Java type: org.cyclos.utils.Page
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#searchKeys(org.cyclos.model.contentmanagement.translations.TranslationQuery)
     */
    public function searchKeys($params) {
        return $this->__run('searchKeys', array($params));
    }
    
    /**
     * @param params Java type: org.cyclos.model.contentmanagement.translations.SetCustomizedTranslationsDTO
     * @see http://documentation.cyclos.org/4.8.1/ws-api-docs/org/cyclos/services/contentmanagement/TranslationMessageService.html#setCustomizedKeysPerLanguage(org.cyclos.model.contentmanagement.translations.SetCustomizedTranslationsDTO)
     */
    public function setCustomizedKeysPerLanguage($params) {
        $this->__run('setCustomizedKeysPerLanguage', array($params));
    }
    
}

?>
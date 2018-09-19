<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class ThemeService extends Service {

    function __construct() {
        parent::__construct('themeService');
    }
    
    /**
     * @param params Java type: org.cyclos.model.contentmanagement.themes.ApplyThemesDTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#applyThemes(org.cyclos.model.contentmanagement.themes.ApplyThemesDTO)
     */
    public function applyThemes($params) {
        $this->__run('applyThemes', array($params));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.server.utils.SerializableInputStream
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#export(java.lang.Long)
     */
    public function export($id) {
        return $this->__run('export', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: java.lang.String
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getCSS(java.lang.Long)
     */
    public function getCSS($id) {
        return $this->__run('getCSS', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getData(java.lang.Long)
     */
    public function getData($id) {
        return $this->__run('getData', array($id));
    }
    
    /**
     * @param params Java type: DP
     * @return Java type: D
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getDataForNew(DP)
     */
    public function getDataForNew($params) {
        return $this->__run('getDataForNew', array($params));
    }
    
    /**
     * @param configuration Java type: org.cyclos.model.system.configurations.ConfigurationVO
     * @return Java type: org.cyclos.model.contentmanagement.themes.ThemesListData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getListData(org.cyclos.model.system.configurations.ConfigurationVO)
     */
    public function getListData($configuration) {
        return $this->__run('getListData', array($configuration));
    }
    
    /**
     * @param type Java type: org.cyclos.model.contentmanagement.themes.ThemeType
     * @return Java type: org.cyclos.model.contentmanagement.themes.ThemeVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getTheme(org.cyclos.model.contentmanagement.themes.ThemeType)
     */
    public function getTheme($type) {
        return $this->__run('getTheme', array($type));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: org.cyclos.model.contentmanagement.themes.ThemeVO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#getVO(java.lang.Long)
     */
    public function getVO($id) {
        return $this->__run('getVO', array($id));
    }
    
    /**
     * @param configuration Java type: org.cyclos.model.system.configurations.ConfigurationVO     * @param importedFromFile Java type: java.lang.String     * @param in Java type: org.cyclos.server.utils.SerializableInputStream
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#importNew(org.cyclos.model.system.configurations.ConfigurationVO,%20java.lang.String,%20org.cyclos.server.utils.SerializableInputStream)
     */
    public function importNew($configuration, $importedFromFile, $in) {
        return $this->__run('importNew', array($configuration, $importedFromFile, $in));
    }
    
    /**
     * @param configuration Java type: org.cyclos.model.system.configurations.ConfigurationVO
     * @return Java type: java.util.List
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#list(org.cyclos.model.system.configurations.ConfigurationVO)
     */
    public function _list($configuration) {
        return $this->__run('list', array($configuration));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @return Java type: DTO
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#load(java.lang.Long)
     */
    public function load($id) {
        return $this->__run('load', array($id));
    }
    
    /**
     * @param id Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#remove(java.lang.Long)
     */
    public function remove($id) {
        $this->__run('remove', array($id));
    }
    
    /**
     * @param ids Java type: java.util.Collection
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#removeAll(java.util.Collection)
     */
    public function removeAll($ids) {
        $this->__run('removeAll', array($ids));
    }
    
    /**
     * @param object Java type: DTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/contentmanagement/ThemeService.html#save(DTO)
     */
    public function save($object) {
        return $this->__run('save', array($object));
    }
    
}

?>
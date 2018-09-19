<?php namespace Cyclos;

/**
 * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/UserGroupService.html
 * 
 * Generated with Cyclos 4.9
 * 
 * WARNING: The API is subject to change between revision versions
 * (for example, 4.5 to 4.6).
 */
class UserGroupService extends Service {

    function __construct() {
        parent::__construct('userGroupService');
    }
    
    /**
     * @param dto Java type: org.cyclos.model.users.groups.ChangeGroupDTO
     * @return Java type: java.lang.Long
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/UserGroupService.html#changeGroup(org.cyclos.model.users.groups.ChangeGroupDTO)
     */
    public function changeGroup($dto) {
        return $this->__run('changeGroup', array($dto));
    }
    
    /**
     * @param locator Java type: org.cyclos.model.users.users.UserLocatorVO
     * @return Java type: org.cyclos.model.users.groups.ChangeGroupData
     * @see http://documentation.cyclos.org/4.9/ws-api-docs/org/cyclos/services/users/UserGroupService.html#getChangeGroupData(org.cyclos.model.users.users.UserLocatorVO)
     */
    public function getChangeGroupData($locator) {
        return $this->__run('getChangeGroupData', array($locator));
    }
    
}

?>
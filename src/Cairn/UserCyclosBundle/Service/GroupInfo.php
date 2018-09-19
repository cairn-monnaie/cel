<?php
// src/Cairn/UserCyclosBundle/Service/GroupInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;
use Cairn\UserCyclosBundle\Service\UserInfo;

/**
 *This class contains getters related to group objects
 *                                                                             
 */
class GroupInfo
{

    /**                                                                        
     * Deals with all groups management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\GroupService $groupService                                            
     */
    private $groupService;

    /**                                                                        
     * UserCyclosBundle service providing getters related to user objects in Cyclos
     *
     *@var UserInfo $userInfoService                                            
     */
    private $userInfoService;

    public function __construct(\Cairn\UserCyclosBundle\Service\UserInfo $userInfoService)
    {
        $this->groupService    = new Cyclos\GroupService();
        $this->userInfoService = $userInfoService;
    }

    /**
     * Provides ID of user $name
     *
     * @param string $name
     * @return int 
     */
    public function getGroupID($name = NULL, $groupType=NULL)
    {
        return $this->getGroupVO($name,$groupType)->id;
    }

    /*
     *get group data
     * @param string $name
     *@return D
     */
    public function getGroup($name)
    {
        return $this->groupService->getData($this->getGroupID($name));
    }

    /**
     * Provides group data for $name
     *
     * Filters the groups by name and type
     *
     * @param string $name
     * @param string $groupType type of group. Java type : org.cyclos.model.users.groups.BasicGroupNature
     * @return stdClass representing Java type : org.cyclos.model.users.groups.GroupVO
     */
    public function getGroupVO($name = NULL, $groupType = NULL)
    {
        $query = new \stdClass();
        $query->name    = is_null($name) ? NULL : $name;
        $query->natures = is_null($groupType) ? NULL : $groupType;

        $res = $this->groupService->search($query);
        if(sizeof($res->pageItems) == 0){
            return NULL;
        }

        return $res->pageItems[0];
    }

    public function getGroupDTO($name = NULL, $groupType = NULL)
    {
        return $this->groupService->load($this->getGroupID($name,$groupType));
    }

    public function getList($groupType)
    {
        $query = new \stdClass();
        $query->natures = $groupType;

        $res = $this->groupService->search($query);
        if(sizeof($res->pageItems) == 0){
            return NULL;
        }

        return $res->pageItems;

    }

    /**
     *Returns true if the group has no user, false otherwise
     *
     *@param string $groupName group's name
     *@return bool
     */
    public function isEmpty($groupName)
    {
        $listUsers =  $this->userInfoService->getListInGroup($groupName); 
        return (sizeof($listUsers) == 0);
    }
}

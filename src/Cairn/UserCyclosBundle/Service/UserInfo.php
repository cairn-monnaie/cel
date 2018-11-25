<?php
// src/Cairn/UserCyclosBundle/Service/UserInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to users in Cyclos
 *                                                                             
 */
class UserInfo
{

    /**
     * Deals with all user management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\UserService $userService                                            
     */
    private $userService;

    /**
     * Deals with all group management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\GroupService $groupService                                            
     */
    private $groupService;

    public function __construct()
    {
        $this->userService = new Cyclos\UserService();
        $this->groupService = new Cyclos\GroupService();
    }

    /**
     * Returns the name of a Cyclos owner object
     *
     * Two cases must be considered:
     *     _$owner is a system user(any admin) : $owner = "SYSTEM" is a string
     *     _$owner is not a system user : $owner is a stdClass object with a property 'display'
     *
     *@param stdClass $owner representing any User object implementing Interface AccountOwner : AccountOwnerLocator, BasicUserDTO, ...
     *@return string 
     */
    public function getOwnerName($owner)
    {
        if(property_exists($owner,'display')){
            return $owner->display;
        }
        return $owner;
    }

    public function getProfileData($userVO)
    {
        return $this->userService->getViewProfileData($userVO);
    }

    /**
     *Loads UserDTO with $id
     *
     *@param int $id User's ID
     *@return stdClass representing Java type: org.cyclos.model.users.users.BasicUserDTO
     */
    public function getUserDTO($id)
    {
        return $this->userService->load($id);
    }

    /**
     *WARNING : use this function very carefully ! the option "keywords" meanss that if you have several users with names such that one is a substring of the other one, Cyclos will return several users, and not necessarily the one you are looking for. That's why, for now, we use it only at installation because there is only one user in the system.
     */
    public function getUserVOByName($name)
    {
        $query = new \stdClass();
        $query->keywords = $name;

        $users = $this->userService->search($query)->pageItems;
        $user = (count($users) == 0) ? NULL : $users[0];
        return $user;
    }

    /**
     *Loads UserVO with $id
     *
     *@param int $id User's ID
     *@return stdClass representing Java type: org.cyclos.model.users.users.UserDetailedVO
     */
    public function getUserVO($id)
    {
        $locator = new \stdClass();
        $locator->id = $id;
        return $this->userService->locate($locator);
    }

    /**
     * Looks for the list of users assigned with the product $productVO
     *
     *@param stdClass|int $productVO 
     *@return array of stdClass representing UserVO objects
     */
    public function getUsersWithProduct($productVO)
    {
        $query = new \stdClass();
        $query->products = $productVO;
        return $this->userService->search($query)->pageItems;
    }



    /**
     *Returns true if $userName belongs to group $groupName, false otherwise
     *
     *@param string $groupName
     *@param string $userName
     *@return bool
     */
    public function isInGroup($groupName, $userName)
    {
        return ($groupName == $this->getUserData($userName)->dto->group->name);
    }

    /*
     * Returns the list of users in group $name
     *
     * @param stdClass|int $groupVO either the whole groupVO object or its ID
     * @return list of users in group $name
     */
    public function getListInGroup($groupVO)
    {
        $query = new \stdClass();
        $query->groups = $groupVO;

        return $this->userService->search($query)->pageItems; 
    }
}

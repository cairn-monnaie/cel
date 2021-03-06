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
     * Deals with all user status management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\UserStatusService $userStatusService                                            
     */
    private $userStatusService;

    /**
     * Deals with all group management.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\GroupService $groupService                                            
     */
    private $groupService;

    private $leadingCompanyName;

    public function __construct($leadingCompanyName)
    {
        $this->userService = new Cyclos\UserService();
        $this->userStatusService = new Cyclos\UserStatusService();
        $this->groupService = new Cyclos\GroupService();
        $this->leadingCompanyName = $leadingCompanyName;
    }

    public function getCurrentUser()
    {
        return $this->userService->getCurrentUser();
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
        return $this->leadingCompanyName;
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
     * WARNING : use this function very carefully ! the option "keywords" means that if you have several users with data such that one 
     * is a substring of the other one, Cyclos will return several users, and not necessarily the one you are looking for.
     * Therefore, use this function if and only if you are 100% sure that the keyword identifies your user : account number, email
     */
    public function getUserVOByKeyword($keyword)
    {
        $query = new \stdClass();
        $query->keywords = $keyword;

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

    public function searchUsers($query)
    {
        return $this->userService->search($query)->pageItems; 
    }

    public function getUserStatus($userID)
    {
        return $this->userStatusService->getData($userID)->status;
    }

    /**
     *Returns true if $userName belongs to group $groupName, false otherwise
     *
     *@param string $groupName
     *@param string $userName
     *@return bool
     */
    public function isInGroup($groupName, $userID)
    {
        return ($groupName == $this->getUserDTO($userID)->group->name);
    }

    /*
     * Returns the list of users in group $groupVO
     *
     * @param stdClass|int $groupVO either the whole groupVO object or its ID
     * @return list of users in group $goupVO
     */
    public function getListInGroup($groupVO, $statuses=NULL)
    {
        $query = new \stdClass();
        $query->groups = $groupVO;
        $query->userStatus = $statuses;

        return $this->userService->search($query)->pageItems; 
    }
}

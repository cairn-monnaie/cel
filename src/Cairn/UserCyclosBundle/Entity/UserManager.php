<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class UserManager
{
    private $userService;
    private $userStatusService;
    private $userGroupService;
    private $userChannelService;

    public function __construct()
    {
        $this->userService = new Cyclos\UserService();
        $this->userChannelService = new Cyclos\UserChannelService();
        $this->userStatusService = new Cyclos\UserStatusService();
        $this->userGroupService = new Cyclos\UserGroupService();
    }

    public function addUser($userDTO,$groupVO,$channelVO)
    {
        $userDTO->group = $groupVO;
        $result = $this->userService->register($userDTO);
        $this->userChannelService->saveChannels($result->user->id,$channelVO);
        return $result->user->id; 
    }

    public function editUser($userDTO)
    {
        return $this->userService->save($userDTO);
    }

    public function changeGroupUser($changeGroupDTO)
    {
        return $this->userGroupService->changeGroup($changeGroupDTO);
    }
    public function changeStatusUser($params)
    {
        $this->userStatusService->changeStatus($params);
    }

}

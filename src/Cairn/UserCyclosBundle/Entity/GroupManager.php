<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class GroupManager
{
    private $groupService;

    public function __construct()
    {
        $this->groupService = new Cyclos\GroupService();
    }


    public function editGroup($groupDTO){
        return $this->groupService->save($groupDTO);
    }

    public function removeGroup($id){
        return $this->groupService->remove($id);}

    public function changeStatusGroup($id,$status){
        $groupDTO = $this->groupService->load($id);
        $groupDTO->enabled = ($status == 'enabled') ? true : false;
        return $this->groupService->save($groupDTO);
    }
}

<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class NetworkManager
{
    private $networkService;

    public function __construct()
    {
        $this->networkService = new Cyclos\NetworkService();
    }

    public function addNetwork($network,$data){
        return $this->networkService->createWithData($network,$data);
    }

    public function removeNetwork($id){
        return $this->networkService->remove($id);}

    public function editNetwork($networkDTO)
    {
        return $this->networkService->save($networkDTO);
    }

}

<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class UserIdentificationManager
{
    private $tokenService;
    private $accessClientService;

    public function __construct()
    {
        $this->tokenService = new Cyclos\TokenService();
        $this->accessClientService = new Cyclos\AccessClientService();
    }

    public function assignToken($userID, $typeInternalName, $token)
    {
        $dto = new \stdClass();
        $dto->tokenType = $typeInternalName;
        $dto->tokenValue = $token;

        $this->tokenService->assign($dto, $userID);
    }

    public function activateUserToken($tokenID)
    {
        $this->tokenService->activatePending($tokenID);
    }


    public function createAccessClient($userID, $type)
    {
        // get data for creating a new one
        $query = new \stdClass();
        $query->user = $userID;
        $query->type = $type;
        $acDTO = $this->accessClientService->getDataForNew($query)->dto;

        //create an access client for user with id $userID
        $acDTO->name = 'client '.time();
        $acID = $this->accessClientService->save($acDTO);
    }

    public function assignAccessClient($accessClientVO)
    {
        $acParams = array('accessClient'=>$accessClientVO->id);
        $activationCode = $this->accessClientService->getActivationCode($acParams);

        //activate access client
        $dto = $this->accessClientService->activate($activationCode,'');
        return $dto->token;
    }

    public function unassignAccessClient($accessClientVO)
    {
        $acParams = array('accessClient'=>$accessClientVO->id);
        $this->accessClientService->unassign($acParams);
    }


}

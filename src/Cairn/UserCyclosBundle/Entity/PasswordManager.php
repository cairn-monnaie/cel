<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class PasswordManager
{
    private $passwordService;

    public function __construct()
    {
        $this->passwordService = new Cyclos\PasswordService();
    }

    public function changePassword($currentPassword, $newPassword, $userVO)
    {
        $dto = new \stdClass();
        $dto->oldPassword = $currentPassword;                                  
        $dto->newPassword = $newPassword;                                    
        $dto->confirmNewPassword = $newPassword;                             
        $dto->user = $userVO; 
        $dto->type = 'login';
        $this->passwordService->change($dto);
    }

}

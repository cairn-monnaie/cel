<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class TransferTypeManager
{
    private $transferTypeService;

    public function __construct()
    {
        $this->transferTypeService = new Cyclos\TransferTypeService();
    }

    public function dataForNew($dataParams)
    {
        return $this->transferTypeService->getDataForNew($dataParams);

    }

    public function editTransferType($transferTypeDTO)
    {
        return $this->transferTypeService->save($transferTypeDTO);
    }

}

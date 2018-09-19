<?php
// src/Cairn/UserCyclosBundle/Service/TransferFeeInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;
//use Cairn\TransferFeeCyclosBundle\configureCyclos;

class TransferFeeInfo
{

    private $transferFeeService;

    public function __construct()
    {
        $this->transferFeeService = new Cyclos\TransferFeeService();
    }

    public function getListTransferFeesVO($transferType)
    {
        return $this->transferFeeService->_list($transferType);
    }

    public function getListGeneratedTransferTypes($transferFeeDTO)
    {
        return $this->transferFeeService->listPossibleGeneratedTransferTypes($transferFeeDTO);
    }

    public function getTransferFeeData($id)
    {
        return $this->transferFeeService->getData($id);
    }


    public function getTransferFeeDTOByID($id)
    {
        return $this->transferFeeService->load($id);
    }

//    /**
//     *provides an instance of TransferFeeDTO in order to create a new one. Cyclos documentation does not provide a way to create 
//     *an instance from nothing. Assumes that there is at least one transfer fee in database
//     */
//    public function getTransferFeeDTOForNew()
//    {
//        $query = new \stdClass();
//        $transferFeeVO = $this->transferFeeService->search($query);
//        if( ){
//            return new \Exception('Pas de TransferFee en base de données. Contacter le service développement');
//        }
//        return $this->getTransferFeeDTOByID($transferFeeVO->id);
//    }
}

<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class TransferFeeManager
{
    private $transferFeeService;

    public function __construct()
    {
        $this->transferFeeService = new Cyclos\TransferFeeService();
    }

    public function dataForNew($dataParams)
    {
        return $this->transferFeeService->getDataForNew($dataParams);

    }

    public function editTransferFee($transferFeeDTO)
    {
        return $this->transferFeeService->save($transferFeeDTO);
    }
    /*
     *allowing webServices + recurringPayments + schedules payments
     *
     *@TODO : set a form at configuration to choose maxInstallements
     */
//    public function configureTransferFees()
//    {
//        $query = new \stdClass();
//        $query->currency = new \stdClass();
//        $query->currency->name = 'euro';
//        $transferFees = $this->transferFeeService->search($query);
//
//        foreach($transferFees->pageItems as $transferFee){
//            $transferFeeDTO = $this->transferFeeService->load($transferFee->id);
//            $transferFeeDTO->channels[] = 'webServices';
//            $transferFeeDTO->allowsRecurringPayments = true;//argument not mentioned in documentation (transferFeeDtO / transferfeeData)
//            $transferFeeDTO->allowsScheduledPayments = true;//but available in list of transfer type options in Cyclos (main Web)
//            $transferFeeDTO->maxInstallments = 12; //
//            $this->transferFeeService->save($transferFeeDTO);
//        }
//    }
}

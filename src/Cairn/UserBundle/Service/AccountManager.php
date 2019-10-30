<?php                                                                          
// src/Cairn/UserBundle/Service/AccountManager.php                             

namespace Cairn\UserBundle\Service;                                      

use Cairn\UserCyclosBundle\Service\NetworkInfo;
use Cairn\UserCyclosBundle\Service\BankingInfo;
use Cairn\UserCyclosBundle\Service\UserInfo;
use Cairn\UserCyclosBundle\Service\AccountInfo;

use Cairn\UserCyclosBundle\Entity\BankingManager;

use Cairn\UserBundle\Entity\Mandate;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Repository\UserRepository;


class AccountManager
{
    protected $bankingService;
    protected $networkService;
    protected $accountService;
    protected $userService;
    protected $anonymous;
    protected $network;
    protected $bankingManager;
    protected $userRepo;

    public function __construct(BankingInfo $bankingService, NetworkInfo $networkService, UserInfo $userService, AccountInfo $accountService, $anonymous, $currency,UserRepository $userRepo)
    {
        $this->bankingService = $bankingService;        
        $this->networkService = $networkService;
        $this->userService = $userService;
        $this->accountService = $accountService;
        $this->anonymous = $anonymous;
        $this->network = $currency;
        $this->userRepo = $userRepo;

        $this->bankingManager = new BankingManager();
    }

    /**
     * Returns the consistent number of operations that should have been done since the mandate beginning
     *
     * For the sake of simplicity, the reference date is '28' of each month, but this is specific to our use
     * because mandates should be honored on 28 of each month
     */
    public function getConsistentOperationsCount(Mandate $mandate, \Datetime $end)
    {
        $interval = $mandate->getBeginAt()->diff($end);

        if($interval->invert == 1){
            return -1;
        }

        if($end->diff($mandate->getEndAt())->invert == 1){
            $end = $mandate->getEndAt();
        }

        $dayBegin = $mandate->getBeginAt()->format('d');
        $dayEnd = $end->format('d');

        $nbOperations = $interval->m;

        //these two conditions should not occur on a same set of data according to mandate validator class
        //which contains dayBegin between 1 and 15
        if($dayEnd < $dayBegin){
            $nbOperations += 1;
        }

        if($dayEnd >= 28){
            $nbOperations += 1;
        }

        return $nbOperations;
    }

    public function isUpToDateMandate(Mandate $mandate)
    {
        $today = new \Datetime();

        $status = $mandate->getStatus();
         if( ($status != Mandate::UP_TO_DATE) && ($status != Mandate::OVERDUE)) {
             return true;
         }
        
        return ($this->getConsistentOperationsCount($mandate, $today) == $mandate->getOperations()->count());
    }


    /**
     * Hydrates operation stakeholder (creditor / debitor) 
     *
     * Cyclos returns an object of type OwnerVO which can be either a string 'SYSTEM' if the stakeholder is a system account or a stdClass if the owner is a member. 
     * For this reason, we must check this before hydrating Operation entity
     *
     * @param Operation $operation  : operation to hydrate 
     * @param stdClass/ string $ownerVO : user basic information on Cyclos side
     * @param string $stakeholder : allows 'from' or 'to' as a value to know which attribute to hydrate in the Operation Entity
     * @see https://documentation.cyclos.org/4.11.2/ws-api-docs/org/cyclos/model/banking/accounts/InternalAccountOwner.html
     */
    public function hydrateStakeholder(Operation $operation, $ownerVO, string $stakeholder)
    {
        if( $ownerVO != 'SYSTEM'){
            $user = $this->userRepo->findOneByUsername($ownerVO->shortDisplay);

            if($stakeholder == 'from'){
                $operation->setDebitor($user);
            }else{
                $operation->setCreditor($user);
            }
        }else{
            $name = $this->userService->getOwnerName($ownerVO);

            if($stakeholder == 'from'){
                $operation->setDebitorName($name);
            }else{
                $operation->setCreditorName($name);
            }
        }

    }

    /**
     * Hydrates operation according to corresponding cyclos transfer 
     *
     * Cyclos returns an object of type TransferVO for each money movement 
     * For this reason, we must check this before hydrating Operation entity
     *
     * @param stdClass $transferVO : informations about the transfer
     * @param const int $type : type of the operation (see Operation macros)
     * @param text $reason : reason of the operation. A transferVO type has no attribute 'description'
     * @see https://documentation.cyclos.org/4.11.2/ws-api-docs/org/cyclos/model/banking/transfers/TransferVO.html
     */
    public function hydrateOperation($transferVO, int $type,$reason)
    {
        $operation = new Operation();

        $operation->setType($type);
        $operation->setReason($reason);
        $operation->setPaymentID($transferVO->id);
        $operation->setFromAccountNumber($transferVO->from->number);
        $operation->setToAccountNumber($transferVO->to->number);
        $operation->setAmount($transferVO->currencyAmount->amount);
        $operation->setExecutionDate(new \Datetime($transferVO->date));

        $this->hydrateStakeholder($operation, $transferVO->from->owner, 'from');
        $this->hydrateStakeholder($operation, $transferVO->to->owner, 'to');

        return $operation;
    }

    public function creditUserAccount(User $creditor, $amount, $type, $reason)
    {
        $credentials = array('username'=>$this->anonymous,'password'=>$this->anonymous);
        $this->networkService->switchToNetwork($this->network,'login',$credentials);

        $paymentData = $this->bankingService->getPaymentData('SYSTEM',$creditor->getCyclosID(),NULL);

        foreach($paymentData->paymentTypes as $paymentType){
            if(preg_match('#credit_du_compte#', $paymentType->internalName)){
                $creditTransferType = $paymentType;
            }
        }

        //get account balance of e-cairns
        $anonymousVO = $this->userService->getCurrentUser();
        $accounts = $this->accountService->getAccountsSummary($anonymousVO->id,NULL);
        foreach($accounts as $account){
            if(preg_match('#compte_de_debit_cairn_numerique#', $account->type->internalName)){
                $debitAccount = $account;
            }
        }

        $availableAmount = $debitAccount->status->balance;

        if($availableAmount >= 0){
            $diff = $availableAmount - $amount;
        }else{
            $diff = -$amount;
        }

        if($diff <= 0 ){
            return NULL;
        }else{
            $res = $this->bankingManager->makeSinglePreview($paymentData,$amount,$reason,$creditTransferType,new \Datetime());
        }
        //preview allows to make sure payment would be executed according to provided data
        $paymentVO = $this->bankingManager->makePayment($res->payment);

        //once payment is done, write symfony equivalent
        $operation = new Operation();
        $operation->setType($type);
        $operation->setReason($reason);
        $operation->setPaymentID($paymentVO->transferId);
        $operation->setFromAccountNumber($res->fromAccount->number);
        $operation->setToAccountNumber($res->toAccount->number);
        $operation->setAmount($res->totalAmount->amount);
        $operation->setDebitorName($this->userService->getOwnerName($res->fromAccount->owner));
        $operation->setCreditor($creditor);

        return $operation;
    }

}

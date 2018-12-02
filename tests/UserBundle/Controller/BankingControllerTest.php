<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\SimpleTransaction;
use Cairn\UserBundle\Entity\RecurringTransaction;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;


use Cyclos;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BankingControllerTest extends BaseControllerTest
{
    function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }

    /**
     *@dataProvider provideTransactionData
     */
    public function testTransactionProcess($debitor,$creditor,$to,$expectForm,$ownsAccount,$isValid,$amount,$day,$month,$year,$frequency)
    {
        $crawler = $this->login($debitor, '@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];

        $debitorICC = $debitorAccount->id;
        $previousBalance = $debitorAccount->status->balance;

        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
        $creditorICC = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditorUser->getUsername())->accountNumber;

        $url = '/banking/transaction/request/'.$to.'-'.$frequency;
        $crawler = $this->client->request('GET',$url);

        if($to == 'new'){
            $this->assertTrue($this->client->getResponse()->isRedirect('/security/card/?url='.$url));

            $crawler = $this->client->followRedirect();
            $crawler = $this->inputCardKey($crawler, '1111');
            $crawler = $this->client->followRedirect();
        }else{
            $this->assertFalse($this->client->getResponse()->isRedirect('/security/card/?url='.$url));
        }

        if(!$expectForm){
            $this->assertTrue($this->client->getResponse()->isRedirect());
        }else{
            if($frequency == 'unique'){
                $form = $crawler->selectButton('cairn_userbundle_simpleoperation_save')->form();
                $form['cairn_userbundle_simpleoperation[amount]']->setValue($amount);
                $form['cairn_userbundle_simpleoperation[fromAccount][accountNumber]']->setValue($debitorICC);
                $form['cairn_userbundle_simpleoperation[toAccount][email]']->setValue($creditorUser->getEmail());
                $form['cairn_userbundle_simpleoperation[toAccount][accountNumber]']->setValue($creditorICC);
                $form['cairn_userbundle_simpleoperation[reason]']->setValue('Test virement simple');
                $form['cairn_userbundle_simpleoperation[description]']->setValue('Test virement simple');
                $form['cairn_userbundle_simpleoperation[executionDate][day]']->select($day);
                $form['cairn_userbundle_simpleoperation[executionDate][month]']->select($month);
                $form['cairn_userbundle_simpleoperation[executionDate][year]']->select($year);
            }            

            $crawler =  $this->client->submit($form);

            if($isValid){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("Récapitulatif")')->count());

                //checker le contenu du récapitulatif
                $form = $crawler->selectButton('form_save')->form();
                $crawler = $this->client->submit($form);
            }else{
                $this->assertTrue($this->client->getResponse()->isRedirect());
            }
        }
    }

    /**
     */
    public function provideTransactionData()
    {
        $today = new \Datetime();
        $day = intval($today->format('d'));
        $month = intval($today->format('m'));
        $year = $today->format('Y');

        $future = date_modify(new \Datetime(),'+1 months');
        $futureDay = intval($future->format('d'));
        $futureMonth = intval($future->format('m'));
        $futureYear = $future->format('Y');

        //valid data
        $baseData = array('login'=>'LaBonnePioche','creditor'=>'MaltOBar','to'=>'new','expectForm'=>true,'ownsAccount'=>true,
            'isValid'=>true,'amount'=>'10', 'day'=>$day,'month'=>$month,'year'=>$year,
            'frequency'=>'unique');

        return array(
            'unique account'=>array_replace($baseData,array('to'=>'self','expectForm'=>false)),
            'no beneficiary'=>array_replace($baseData,array('login'=>'DrDBrew', 'to'=>'beneficiary','expectForm'=>false)),
            'debitor does not own account'=>array_replace($baseData,array('ownsAccount'=>false,'isValid'=>false)),
            'not beneficiary'=>array_replace($baseData,array('to'=>'beneficiary','creditor'=>'DrDBrew','isValid'=>false)),
            'valid immediate'=>$baseData,
            'valid scheduled'=>array_replace($baseData,array('day'=>$futureDay,'month'=>$futureMonth,'year'=>$futureYear)), 
            'valid scheduled 2'=>array_replace($baseData,array('day'=>$futureDay,'month'=>$futureMonth,'year'=>$futureYear)), 
            'valid scheduled 3'=>array_replace($baseData,array('day'=>$futureDay,'month'=>$futureMonth,'year'=>$futureYear)), 
//            'valid recurring'=>array_replace($baseData,array('frequency'=>'recurring')), 
        );
    }

    /**
     *
     *@dataProvider provideTransactionDataForValidation
     */
    public function testTransactionValidator($frequency,$amount, $description, $fromAccount, $toAccount,$firstDate,$lastDate,
        $periodicity, $isValid)
    {
        $credentials = array('username'=>$this->testAdmin,'password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'),
                                                                                 'login',$credentials);

        $validator = $this->container->get('validator');

        if($frequency == 'unique'){
            $transaction = new Operation();
            $transaction->setExecutionDate($firstDate); 
        }//else{
         //   $transaction = new RecurringTransaction();
         //   $transaction->setFirstOccurrenceDate($firstDate); 
         //   $transaction->setLastOccurrenceDate($lastDate); 
         //   $transaction->setPeriodicity($periodicity);
        //}
        $transaction->setAmount($amount); 
        $transaction->setReason('test Virement');
        $transaction->setDescription($description); 
        $transaction->setFromAccount($fromAccount); 
        $transaction->setToAccount($toAccount); 

        $errors = $validator->validate($transaction);

        if($isValid){
            $this->assertEquals(0,count($errors));
        }else{
            $this->assertEquals(1,count($errors));
        }
    }

    public function provideTransactionDataForValidation()
    {
        $creditor = 'MaltOBar';
        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
        $creditorICC = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditorUser->getUsername())->accountNumber;

        $debitor = 'LaBonnePioche';
        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];
        $debitorICC = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditorUser->getUsername())->accountNumber;

        $baseData = array('frequency'=>'unique','amount'=>'10','description'=>'Test Validator',
            'fromAccount'=> array('id'=>$debitorICC ,'email'=>$debitorUser->getEmail()),
            'toAccount'=> array('id'=>$creditorICC ,'email'=>$creditorUser->getEmail()),
            'firstDate'=> new \Datetime(),'lastDate'=>date_modify(new \Datetime(),'+1 months') ,'periodicity'=>'1', 'isValid'=>true );

        return array(
            'negative amount'=>array_replace($baseData,array('amount'=>'-1','isValid'=>false)),
            'undersize amount'=>array_replace($baseData,array('amount'=>'0.0099','isValid'=>false)),
            'wrong debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>array('id'=>$debitorICC + 1),'isValid'=>false)),
            'wrong creditor ICC'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>$creditorICC + 1),'isValid'=>false)),
            'wrong creditor email'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>'','email'=>'test@test.com'),
            'isValid'=>false)),
            'no creditor data'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>'','email'=>''),'isValid'=>false)),
            'no creditor ICC'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>''))),
            'no debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>array('id'=>''),'isValid'=>false)),
            'identical accounts'=>array_replace($baseData,array(
                'toAccount'=>array('id'=>$debitorAccount->id,'email'=>$debitorUser->getEmail()),
                'isValid'=>false)),
            'insufficient balance' =>array_replace($baseData,array('amount'=>'1000000','isValid'=>false)),
            'inconsistent date'=>array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'+4 years'),'isValid'=>false)),
            'date before today' =>array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
//            'first date b4 today' =>array_replace($baseData,array('frequency'=>'recurring',
//            'firstDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
//            'last date b4 first' =>array_replace($baseData,array('frequency'=>'recurring',
//            'lastDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
//            'too short interval' =>array_replace($baseData,array('frequency'=>'recurring',
//            'lastDate'=>date_modify(new \Datetime(),'+10 days'),'isValid'=>false)),
//            'too short interval 2' =>array_replace($baseData,array('frequency'=>'recurring','periodicity'=>'2',
//            'lastDate'=>date_modify(new \Datetime(),'+35 days'),'isValid'=>false)),

            'valid simple'=> $baseData,
            'valid scheduled'=> array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'+1 months'),'isValid'=>true)),

//            'valid recurring'=> array_replace($baseData,array('frequency'=>'recurring'))
        );
    }

//    /**
//     *@dataProvider provideDataForConversion
//     */
//    public function testConversion($executor, $creditor, $to, $isValid)
//    {
//
//        $crawler = $this->login($executor, '@@bbccdd');
//
//        $executorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$executor));
//        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getDebitAccount();
//        $debitorICC = $debitorAccount->id;
//
//        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
//        $creditorICC = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0]->id;
//
//        $url = 'banking/conversion/request?to='.$to;
//        $crawler = $this->client->request('GET',$url);
//
//        $crawler = $this->client->followRedirect();
//        $crawler = $this->inputCardKey($crawler, '1111');
//        $crawler = $this->client->followRedirect();
//
//        $form = $crawler->selectButton('cairn_userbundle_simpletransaction_save')->form();
//        $form['cairn_userbundle_simpletransaction[amount]']->setValue(100);
//        $form['cairn_userbundle_simpletransaction[toAccount][id]']->setValue($creditorICC);
//        $form['cairn_userbundle_simpletransaction[description]']->setValue('Test conversion simple');
//
//        $crawler =  $this->client->submit($form);
//
//        if($isValid){
//            $crawler = $this->client->followRedirect();
//            $this->assertSame(1,$crawler->filter('html:contains("Récapitulatif")')->count());
//
//            //checker le contenu du récapitulatif
//            $form = $crawler->selectButton('form_save')->form();
//            $crawler = $this->client->submit($form);
//        }else{
//            $this->assertTrue($this->client->getResponse()->isRedirect());
//        }
//    }
//
//
//    public function provideDataForConversion()
//    {
//        $baseOtherData = array('executor'=>$this->testAdmin,'creditor'=>'LaBonnePioche','to'=>'other','isValid'=>true);
//        $baseSelfData = array('executor'=>'LaBonnePioche','creditor'=>'LaBonnePioche','to'=>'self', 'isValid'=>true);
//
//        return array(
//            'sys for other but sys account provided'=>array_replace($baseOtherData,array('creditor'=>$this->testAdmin,'isValid'=>false)),
//            'sys for himself but other account provided'=>array_replace($baseSelfData,array('executor'=>$this->testAdmin,'isValid'=>false)),
//            'user for himself but other account provided'=>array_replace($baseSelfData,array('creditor'=>'MaltOBar','isValid'=>false)),
//            'valid user conversion' => $baseSelfData,
//            'valid self conversion sys' => array_replace($baseSelfData,array('executor'=>$this->testAdmin,'creditor'=>$this->testAdmin)),
//            'valid other conversion sys' => $baseOtherData,
//            //
//        );
//    }

    /**
     *
     *@todo : change traversing methods when css added. Putting raw values for selectLink method is more prone to errors than a div.class
     *@dataProvider provideDownloadersAndOwners
     */
    public function testDownloadRIB($downloader,$owner,$isLegit)
    {
        $crawler = $this->login($downloader, '@@bbccdd');

        $downloaderUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$downloader));
        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$owner));

        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
        if(!$isLegit){
            //assert isRedirect homepage "Donnée introuvable"
            $this->assertTrue($this->client->getResponse()->isRedirect());
            $crawler = $this->client->followRedirect();
            $this->assertSame(1,$crawler->filter('html:contains("Donnée introuvable")')->count());

        }else{
            $crawler = $this->client->request('GET','banking/download/rib/'.$ownerAccount->id);
            $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','application/pdf'));
        }

    }

    public function provideDownloadersAndOwners()
    {
        return array(
            'admin for pro'=>array('downloader'=>$this->testAdmin,'owner'=>'DrDBrew','isLegit'=>true ),
            'pro for himself'=>array('downloader'=>'LaBonnePioche','owner'=>'LaBonnePioche','isLegit'=>true ),
            'pro for admin'=>array('downloader'=>'LaBonnePioche','owner'=>'glGrenoble','isLegit'=>false ),
            'pro for pro'=>array('downloader'=>'LaBonnePioche','owner'=>'DrDBrew','isLegit'=>false ),
            'admin for non referred'=>array('downloader'=>$this->testAdmin,'owner'=>'cafeEurope','isLegit'=>false ),
        );
    }

    /**
     *@todo : add tick option when a user will have several accounts
     *@dataProvider provideDataForAccountsOverview
     */
    public function testDownloadAccountsOverview($downloader,$isLegit,$tick,$format,$isValid,$endDay,$endMonth,$endYear,$pastDay,
        $pastMonth,$pastYear)
    {
        $crawler = $this->login($downloader, '@@bbccdd');

        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$downloader));

        $url = '/banking/download/accounts/';
        $crawler = $this->client->request('GET',$url);

        if(!$isLegit){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }else{
            $form = $crawler->selectButton('form_save')->form();
            $form['form[format]']->select($format);

            //            if($tick){
            //                $form['form[accounts][]']->tick();
            //            }
            $form['form[begin][day]']->select($pastDay);
            $form['form[begin][month]']->select($pastMonth);
            $form['form[begin][year]']->select($pastYear);

            $form['form[end][day]']->select($endDay);
            $form['form[end][month]']->select($endMonth);
            $form['form[end][year]']->select($endYear);

            $crawler =  $this->client->submit($form);

            if($isValid){
                if($format == 'pdf'){
                    $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','application/pdf'));
                }else{
                    $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','application/force-download'));
                }
            }else{
                $this->assertTrue($this->client->getResponse()->isRedirect($url));
            }
        }
    }

    public function provideDataForAccountsOverview()
    {
        $today = new \Datetime();
        $day = intval($today->format('d'));
        $month = intval($today->format('m'));
        $year = $today->format('Y');

        $past = date_modify(new \Datetime(),'-1 months');
        $pastDay = intval($past->format('d'));
        $pastMonth = intval($past->format('m'));
        $pastYear = $past->format('Y');

        //valid Data
        $baseData = array('downloader'=>'LaBonnePioche','isLegit'=>true,'tick'=>true,'format'=>'csv','isValid'=>true,
            'endDay'=>$day,'endMonth'=>$month,'endYear'=>$year,
            'pastDay'=>$pastDay,'pastMonth'=>$pastMonth,'pastYear'=>$pastYear,
        );

        return array(
            'pro'=>$baseData,
            'admin'=>array_replace($baseData,array('downloader'=>'glGrenoble','isLegit'=>false)),
            'super_admin'=>array_replace($baseData,array('downloader'=>$this->testAdmin,'isLegit'=>true,'format'=>'pdf')),
            'anonym'=>array_replace($baseData,array('downloader'=>'','isLegit'=>false)),
            'today=before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year)),
            'today<before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year,
            'endDay'=>$pastDay,'endMonth'=>$pastMonth,'endYear'=>$pastYear)),
        );
    }

    /**
     *@TODO : execute -> test that operation type has changed
     *        cancel -> test that operation has been removed
     *If testSimpleTransactionProcess ends correctly, there are two scheduled payments made by LaBonnePioche
     *@depends testSimpleTransactionProcess
     */
    public function testChangeScheduledTransactionStatus($newStatus)
    {
        $owner = 'LaBonnePioche';
        $crawler = $this->login($owner, '@@bbccdd');
        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$owner));
        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
        $accountBalanceBefore = $ownerAccount->status->balance;

        $scheduledOperations = $operationRepo->createQueryBuilder('o')
            ->where('o.fromAccountNumber = :number')
            ->andWhere('o.paymentID is not NULL')                      
            ->andWhere('o.type = :type')                               
            ->setParameter('type',Operation::$TYPE_TRANSACTION_SCHEDULED)
            ->setParameter('number',$ownerAccount->number)                  
            ->getQuery()->getResult(); 

        $operation = $scheduledOperations[0];
        if($newStatus == 'execute'){
            $link = $crawler->filter('a[href*="'.$operation->getPaymentID().'-'.$newStatus.'"]')->eq(0)->link();
            $crawler = $this->client->click($link);
            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);
            $crawler = $this->client->followRedirect();
            $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            $this->em->refresh($operation);
            $this->assertEquals($operation->getType(), $TYPE_TRANSACTION_EXECUTED);
            $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
            $accountBalanceAfter = $ownerAccount->status->balance;

            $this->assertTrue($accountBalanceAfter < $accountBalanceBefore);
        }else{
            $link = $crawler->filter('a[href*="'.$operation->getPaymentID().'-'.$newStatus.'"]')->eq(0)->link();
            $crawler = $this->client->click($link);
            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);
            $crawler = $this->client->followRedirect();
            $this->assertSame(1, $crawler->filter('div.alert-success')->count());    

            $this->em->refresh($operation);
            $this->assertEquals($operation,NULL);
            $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
            $accountBalanceAfter = $ownerAccount->status->balance;

            $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);

        }
    }

}

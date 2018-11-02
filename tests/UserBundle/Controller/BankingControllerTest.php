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

use Cyclos;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BankingControllerTest extends BaseControllerTest
{
    function __construct($name = NULL, array $data = array(), $dataName = ''){
        parent::__construct($name, $data, $dataName);
    }

    /**
     *@dataProvider provideSimpleTransactionData
     */
    public function testSimpleTransactionProcess($debitor,$creditor,$to,$expectForm,$ownsAccount,$isValid,$amount,$day,$month,$year)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $crawler = $this->login($debitor, '@@bbccdd');

        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];

        if(!$ownsAccount){
            $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getDebitAccount();
        }

        $debitorICC = $debitorAccount->id;
        $previousBalance = $debitorAccount->status->balance;

        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
        $creditorICC = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0]->id;

        $url = '/banking/transaction/request/'.$to.'-unique';
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
            $form = $crawler->selectButton('cairn_userbundle_simpletransaction_save')->form();
            $form['cairn_userbundle_simpletransaction[amount]']->setValue($amount);
            $form['cairn_userbundle_simpletransaction[fromAccount][email]']->setValue($debitorUser->getEmail());
            $form['cairn_userbundle_simpletransaction[fromAccount][id]']->setValue($debitorICC);
            $form['cairn_userbundle_simpletransaction[toAccount][email]']->setValue($creditorUser->getEmail());
            $form['cairn_userbundle_simpletransaction[toAccount][id]']->setValue($creditorICC);
            $form['cairn_userbundle_simpletransaction[description]']->setValue('Test virement simple');
            $form['cairn_userbundle_simpletransaction[date][day]']->select($day);
            $form['cairn_userbundle_simpletransaction[date][month]']->select($month);
            $form['cairn_userbundle_simpletransaction[date][year]']->select($year);

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
    public function provideSimpleTransactionData()
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
            'isValid'=>true,'amount'=>'10', 'day'=>$day,'month'=>$month,'year'=>$year);

        return array(
            'unique account'=>array_replace($baseData,array('to'=>'self','expectForm'=>false)),
            'no beneficiary'=>array_replace($baseData,array('login'=>'DrDBrew', 'to'=>'beneficiary','expectForm'=>false)),
            'debitor does not own account'=>array_replace($baseData,array('ownsAccount'=>false,'isValid'=>false)),
            'not beneficiary'=>array_replace($baseData,array('to'=>'beneficiary','creditor'=>'DrDBrew','isValid'=>false)),
            'valid immediate'=>$baseData,
            'valid scheduled'=>array_replace($baseData,array('day'=>$futureDay,'month'=>$futureMonth,'year'=>$futureYear)), 
        );
    }

    /**
     *
     *@dataProvider provideTransactionDataForValidation
     */
    public function testTransactionValidator($frequency,$amount, $description, $fromAccount, $toAccount,$firstDate,$lastDate,
        $periodicity, $isValid)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $validator = $this->container->get('validator');

        if($frequency == 'unique'){
            $transaction = new SimpleTransaction();
            $transaction->setDate($firstDate); 
        }else{
            $transaction = new RecurringTransaction();
            $transaction->setFirstOccurrenceDate($firstDate); 
            $transaction->setLastOccurrenceDate($lastDate); 
            $transaction->setPeriodicity($periodicity);
        }
        $transaction->setAmount($amount); 
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
        $creditorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($creditorUser->getCyclosID())[0];
        $creditorICC = $creditorAccount->id;

        $debitor = 'LaBonnePioche';
        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];
        $debitorICC = $debitorAccount->id;

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
            'first date b4 today' =>array_replace($baseData,array('frequency'=>'recurring',
            'firstDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
            'last date b4 first' =>array_replace($baseData,array('frequency'=>'recurring',
            'lastDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
            'too short interval' =>array_replace($baseData,array('frequency'=>'recurring',
            'lastDate'=>date_modify(new \Datetime(),'+10 days'),'isValid'=>false)),
            'too short interval 2' =>array_replace($baseData,array('frequency'=>'recurring','periodicity'=>'2',
            'lastDate'=>date_modify(new \Datetime(),'+35 days'),'isValid'=>false)),

            'valid simple'=> $baseData,
            'valid recurring'=> array_replace($baseData,array('frequency'=>'recurring'))
        );
    }

    /**
     *
     *@todo : change traversing methods when css added. Putting raw values for selectLink method is more prone to errors than a div.class
     *@dataProvider provideDownloadersAndOwners
     */
    public function testDownloadRIB($downloader,$owner,$isLegit)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $downloaderUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$downloader));

        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$owner));
        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];

        $crawler = $this->login($downloader, '@@bbccdd');

        $crawler = $this->client->request('GET','banking/download/rib/'.$ownerAccount->id);

        if($isLegit){
            $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','application/pdf'));
        }else{
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        }
    }

    public function provideDownloadersAndOwners()
    {
        return array(
            'admin for pro'=>array('downloader'=>'mazouthm','owner'=>'DrDBrew','isLegit'=>true ),
            'pro for himself'=>array('downloader'=>'LaBonnePioche','owner'=>'LaBonnePioche','isLegit'=>true ),
            'pro for admin'=>array('downloader'=>'LaBonnePioche','owner'=>'glGrenoble','isLegit'=>false ),
            'pro for pro'=>array('downloader'=>'LaBonnePioche','owner'=>'DrDBrew','isLegit'=>false ),
            'admin for non referred'=>array('downloader'=>'mazouthm','owner'=>'cafeEurope','isLegit'=>false ),
        );
    }

    /**
     *@todo : add tick option when a user will have several accounts
     *@dataProvider provideDataForAccountsOverview
     */
    public function testDownloadAccountsOverview($downloader,$isLegit,$tick,$format,$isValid,$endDay,$endMonth,$endYear,$pastDay,
                                                 $pastMonth,$pastYear)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

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
            'super_admin'=>array_replace($baseData,array('downloader'=>'mazouthm','isLegit'=>true,'format'=>'pdf')),
            'anonym'=>array_replace($baseData,array('downloader'=>'','isLegit'=>false)),
            'today=before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year)),
            'today<before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year,
                                                          'endDay'=>$pastDay,'endMonth'=>$pastMonth,'endYear'=>$pastYear)),
        );
    }

//    /**
//     *
//     *@depends testSimpleTransactionProcess
//     */
//    public function testChangeScheduledTransactionStatus()
//    {
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));
//
//        $crawler = $this->login('LaBonnePioche', '@@bbccdd');
//
//        $crawler = $this->crawler->filter('a[href*=""]')->
//    }
}

<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserCyclosBundle\Entity\ScriptManager;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;

use Cyclos;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class BankingControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    /**
     *@dataProvider provideTransactionData
     */
    public function testTransactionProcess($debitor,$to,$expectForm,$ownsAccount,$isValid,$toAccount,$amount,$date,$frequency,$confirmCode)
    {
        $crawler = $this->login($debitor, '@@bbccdd');

        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $debitorUser = $userRepo->findOneBy(array('username'=>$debitor));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];

        $debitorICC = $debitorAccount->number;
        $previousBalance = $debitorAccount->status->balance;

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
            $this->assertTrue($this->client->getResponse()->isRedirect('/banking/transaction/new/to/'.$frequency));
        }else{
            if($frequency == 'unique'){
                $form = $crawler->selectButton('cairn_userbundle_simpleoperation_save')->form();
                $form['cairn_userbundle_simpleoperation[amount]']->setValue($amount);
                $form['cairn_userbundle_simpleoperation[fromAccount]']->setValue($debitorICC);
                $form['cairn_userbundle_simpleoperation[toAccount]']->setValue($toAccount);
                $form['cairn_userbundle_simpleoperation[reason]']->setValue('Test virement simple');
                $form['cairn_userbundle_simpleoperation[description]']->setValue('Test virement simple');
                $form['cairn_userbundle_simpleoperation[executionDate]']->setValue($date);
            }            

            $crawler =  $this->client->submit($form);

            file_put_contents('test.txt',$this->client->getResponse()->getContent());
            if($isValid){
                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("Récapitulatif")')->count());

                //todo : checker le contenu du récapitulatif
                $form = $crawler->selectButton('form_save')->form();
//                $form['form[confirmationCode]']->setValue($confirmCode);
                $crawler = $this->client->submit($form);

//                if($confirmCode == '1111'){
                    $this->assertTrue($this->client->getResponse()->isRedirect());
                    $crawler = $this->client->followRedirect();
                    $this->assertSame(1,$crawler->filter('html:contains("succès")')->count());
//                }else{
//                    $this->assertTrue($this->client->getResponse()->isRedirect());
//                    $crawler = $this->client->followRedirect();
//                    $this->assertSame(1,$crawler->filter('html:contains("erroné")')->count());
//                }
            }else{
                $this->assertTrue($this->client->getResponse()->isRedirect('/banking/transaction/request/'.$to.'-'.$frequency));
            }
        }
    }

    /**
     */
    public function provideTransactionData()
    {
        $today = new \Datetime();
        $today_format = $today->format('Y-m-d');

        $future = date_modify(new \Datetime(),'+1 months');
        $future_format = $future->format('Y-m-d');

        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $creditorUser = $userRepo->findOneBy(array('username'=>'maltobar'));
        $creditorEmail = $creditorUser->getEmail();
        $creditorICC = $creditorUser->getMainICC();

        //valid data
        //User needs to have a phone number
        $baseData = array('login'=>'nico_faus_prod','to'=>'new','expectForm'=>true,'ownsAccount'=>true,
            'isValid'=>true,'toAccount'=>$creditorEmail,'amount'=>'10', 'date'=>$today_format, 'frequency'=>'unique','confirmCode'=>'1111');

        return array(
            'unique account'=>array_replace($baseData,array('to'=>'self','expectForm'=>false)),
            'has no beneficiary'=>array_replace($baseData,array('login'=>'maltobar', 'to'=>'beneficiary','expectForm'=>false)),
            'has beneficiary, data matches no beneficiary'=>array_replace($baseData,array('login'=>'nico_faus_prod','to'=>'beneficiary',
                                                            'toAccount'=>$creditorICC, 'isValid'=>false)),
//            'invalid confirmation code'=>array_replace($baseData,array('confirmCode'=>'2222')),
            'valid immediate with email'=>$baseData,
            'valid with ICC'=>array_replace($baseData,array('toAccount'=>$creditorICC)),
            'valid email, future date'=>array_replace($baseData,array('date'=>$future_format)), 
            'valid account number, future date'=>array_replace($baseData,array('toAccount'=>$creditorICC,'date'=>$future_format)), 
//           'valid recurring'=>array_replace($baseData,array('frequency'=>'recurring')), 
        );
    }

//    /**
//     *
//     *@dataProvider provideTransactionDataForValidation
//     */
//    public function testTransactionValidator($frequency,$amount, $description, $fromAccount, $toAccount,$firstDate,$lastDate,$periodicity, $isValid)
//    {
//        $credentials = array('username'=>'labonnepioche','password'=>'@@bbccdd');
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),
//                                                                                 'login',$credentials);
//
//        $validator = $this->container->get('validator');
//
//        if($frequency == 'unique'){
//            $transaction = new Operation();
//            $transaction->setExecutionDate($firstDate); 
//        }//else{
//         //   $transaction = new RecurringTransaction();
//         //   $transaction->setFirstOccurrenceDate($firstDate); 
//         //   $transaction->setLastOccurrenceDate($lastDate); 
//         //   $transaction->setPeriodicity($periodicity);
//        //}
//        $transaction->setAmount($amount); 
//        $transaction->setReason('test Virement');
//        $transaction->setDescription($description); 
//        $transaction->setFromAccount($fromAccount); 
//        $transaction->setToAccount($toAccount); 
//
//        $errors = $validator->validate($transaction);
//
//        if($isValid){
//            $this->assertEquals(0,count($errors));
//        }else{
//            $this->assertEquals(1,count($errors));
//        }
//    }
//
//    public function provideTransactionDataForValidation()
//    {
//        $debitor = 'labonnepioche';
//
//        $credentials = array('username'=>$debitor,'password'=>'@@bbccdd');
//        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),
//                                                                                 'login',$credentials);
//
//        $creditor = 'maltobar';
//        $creditorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$creditor));
//        $creditorICC = $this->container->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($creditor)->accountNumber;
//
//        $debitorUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$debitor));
//        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];
//        $debitorICC = $debitorAccount->number;
//
//        $baseData = array('frequency'=>'unique','amount'=>'10','description'=>'Test Validator',
//            'fromAccount'=> array('number'=>$debitorICC),
//            'toAccount'=> array('number'=>$creditorICC),
//            'firstDate'=> new \Datetime(),'lastDate'=>date_modify(new \Datetime(),'+1 months') ,'periodicity'=>'1', 'isValid'=>true );
//
//        return array(
//            'negative amount'=>array_replace($baseData,array('amount'=>'-1','isValid'=>false)),
//            'undersize amount'=>array_replace($baseData,array('amount'=>'0.0099','isValid'=>false)),
//            'wrong debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>json_decode(json_encode($debitorAccount))),
//                                                                                              'isValid'=>false)),
//            'wrong creditor ICC'=>array_replace_recursive($baseData,array('toAccount'=>array('number'=>$creditorICC + 1),
//                                                                                             'isValid'=>false)),
//            'no creditor data'=>array_replace_recursive($baseData,array('toAccount'=>array('number'=>''),
//                                                                        'isValid'=>false)),
//            'no creditor ICC'=>array_replace_recursive($baseData,array('toAccount'=>array('number'=>''))),
//            'no debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>array('number'=>''),'isValid'=>false)),
//            'identical accounts'=>array_replace($baseData,array(
//                'toAccount'=>array('number'=>$debitorAccount->number),
//                'isValid'=>false)),
//            'insufficient balance' =>array_replace($baseData,array('amount'=>'1000000','isValid'=>false)),
//            'inconsistent date'=>array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'+4 years'),'isValid'=>false)),
//            'date before today' =>array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
////            'first date b4 today' =>array_replace($baseData,array('frequency'=>'recurring',
////            'firstDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
////            'last date b4 first' =>array_replace($baseData,array('frequency'=>'recurring',
////            'lastDate'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
////            'too short interval' =>array_replace($baseData,array('frequency'=>'recurring',
////            'lastDate'=>date_modify(new \Datetime(),'+10 days'),'isValid'=>false)),
////            'too short interval 2' =>array_replace($baseData,array('frequency'=>'recurring','periodicity'=>'2',
////            'lastDate'=>date_modify(new \Datetime(),'+35 days'),'isValid'=>false)),
//
//            'valid simple ICC'=> $baseData,
//            'valid simple ICC with spaces'=> array_replace_recursive($baseData, array(
//                                    'toAccount'=>array('ICC'=>substr_replace($creditorICC,' ',2,0) ))),
//            'valid scheduled'=> array_replace($baseData,array('firstDate'=>date_modify(new \Datetime(),'+1 months'),'isValid'=>true)),
////            'valid recurring'=> array_replace($baseData,array('frequency'=>'recurring'))
//        );
//    }


    /**
     *@dataProvider provideDataForReconversion
     */
    public function testReconversion($current,$amount,$expectForm, $isValid,$expectedMessage)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $userRepo = $this->em->getRepository('CairnUserBundle:User');

        $debitorUser = $userRepo->findOneBy(array('username'=>$current));
        $debitorAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];

        $debitorICC = $debitorAccount->number;
        $accountBalanceBefore = $debitorAccount->status->balance;

        $url = '/banking/reconversion';
        $crawler = $this->client->request('GET',$url);

        
        if(!$expectForm){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }else{
            $this->client->enableProfiler();

            $form = $crawler->selectButton('cairn_userbundle_reconversion_save')->form();
            $form['cairn_userbundle_reconversion[amount]']->setValue($amount);
            $form['cairn_userbundle_reconversion[fromAccount]']->setValue($debitorICC);
            $form['cairn_userbundle_reconversion[reason]']->setValue('Test reconversion');
            $form['cairn_userbundle_reconversion[description]']->setValue('Test reconversion description');

            $crawler =  $this->client->submit($form);

            $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($debitorUser->getCyclosID())[0];
            $accountBalanceAfter = $ownerAccount->status->balance;

            if($isValid){
                $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

                //assert email
                $this->assertSame(2, $mailCollector->getMessageCount());

                $message = $mailCollector->getMessages()[0];

                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('Reconversion', $message->getSubject());
                $this->assertContains($amount, $message->getBody());
                $this->assertContains($debitorUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($debitorUser->getEmail(), key($message->getTo()));

                $message = $mailCollector->getMessages()[1];

                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('Reconversion', $message->getSubject());
                $this->assertContains($amount, $message->getBody());
                $this->assertContains($debitorUser->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($this->container->getParameter('cairn_email_management'), key($message->getTo()));


                $crawler = $this->client->followRedirect();
                $this->assertSame(1,$crawler->filter('html:contains("Détail de votre reconversion")')->count());

                $this->assertTrue($accountBalanceAfter == ($accountBalanceBefore - $amount));

                
            }else{
                $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
                $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);
            }
        }
    }


    public function provideDataForReconversion()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'invalid : access disabled to persons'=> array('comblant_michel','40',false,false,''),
            'invalid : access disabled to admins'=> array($adminUsername,'40',false,false,''),
            'valid : pro with non null account'=> array('nico_faus_prod','100',true,true,''),
            'invalid : amount too high'=> array('nico_faus_prod','1000000',true,false,'Montant trop élevé'),
        );

    }


    /**
     *
     *@todo : change traversing methods when css added. Putting raw values for selectLink method is more prone to errors than a div.class
     *@dataProvider provideDownloadersAndOwners
     */
    public function testDownloadRIB($downloader,$owner,$isCyclosLegit, $isLegit)
    {
        //connect with admin to get owner's account ids (not possible with member for another one)
        $credentials = array('username'=>$this->testAdmin,'password'=>'@@bbccdd');
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_currency_cairn'),
                                                                                 'login',$credentials);

        $downloaderUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$downloader));
        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$owner));

        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];

        $crawler = $this->login($downloader, '@@bbccdd');
        $crawler = $this->client->request('GET','banking/download/rib/'.$ownerAccount->id);

        if(!$isCyclosLegit){
            //assert isRedirect homepage "Donnée introuvable"
            $this->assertTrue($this->client->getResponse()->isRedirect());
            $crawler = $this->client->followRedirect();
            
            $this->assertSame(1,$crawler->filter('html:contains("Donnée introuvable")')->count());
        }else{
            if($isLegit){
                $this->assertTrue($this->client->getResponse()->headers->contains('Content-Type','application/pdf'));
            }else{
                $this->assertTrue($this->client->getResponse()->isRedirect('/'));
                $crawler = $this->client->followRedirect();

                $this->assertSame(1,$crawler->filter('html:contains("droits nécessaires")')->count());
            }
        }

    }

    public function provideDownloadersAndOwners()
    {
        $adminUsername = $this->testAdmin;

        return array(
            'superadmin for pro'=>array('downloader'=>$adminUsername,'owner'=>'labonnepioche','isCyclosLegit'=>true,'isLegit'=>true),
            'superadmin for person'=>array('downloader'=>$adminUsername,'owner'=>'gjanssens','isCyclosLegit'=>true,'isLegit'=>true),
            'pro for himself'=>array('downloader'=>'labonnepioche','owner'=>'labonnepioche','isCyclosLegit'=>true,'isLegit'=>true),
            'person for himself'=>array('downloader'=>'gjanssens','owner'=>'gjanssens','isCyclosLegit'=>true,'isLegit'=>true),
            'pro for admin'=>array('downloader'=>'labonnepioche','owner'=>$adminUsername,'isCyclosLegit'=>false,'isLegit'=>false),
            'pro for pro'=>array('downloader'=>'labonnepioche','owner'=>'maltobar','isCyclosLegit'=>false,'isLegit'=>false),
            'person for person'=>array('downloader'=>'gjanssens','owner'=>'comblant_michel','isCyclosLegit'=>false,'isLegit'=>false),
            'pro for person'=>array('downloader'=>'labonnepioche','owner'=>'gjanssens','isCyclosLegit'=>false,'isLegit'=>false),
            'person for pro'=>array('downloader'=>'gjanssens','owner'=>'labonnepioche','isCyclosLegit'=>false,'isLegit'=>false),
            'superadmin for non referred'=>array('downloader'=>$adminUsername,'owner'=>'NaturaVie','isCyclosLegit'=>true,'isLegit'=>false),
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
        $baseData = array('downloader'=>'labonnepioche','isLegit'=>true,'tick'=>true,'format'=>'csv','isValid'=>true,
            'endDay'=>$day,'endMonth'=>$month,'endYear'=>$year,
            'pastDay'=>$pastDay,'pastMonth'=>$pastMonth,'pastYear'=>$pastYear,
        );

        $adminUsername = $this->testAdmin;

        return array(
            'pro'=>$baseData,
            'person'=>array_replace($baseData,array('downloader'=>'cretine_agnes')),
            'today=before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year)),
            'today<before'=>array_replace($baseData,array('isValid'=>false,'pastDay'=>$day,'pastMonth'=>$month,'pastYear'=>$year,
                                                          'endDay'=>$pastDay,'endMonth'=>$pastMonth,'endYear'=>$pastYear)),
        );
    }

    /**
     * WARNING : in all tests, the database is rolled back to the initial stable state once the test has finished.                         
     * We cannot do that for this specific test, as the action impacts a payment on cyclos-side, which can not be rolled back.
     * In order for this test to be consistent and reiterable several times, we must commit modifications on DB
     *
     * At some point, if this test is reiterated too many times, there will not be scheduled payment to test, which will result in a test
     * error. The whole testing process will need to be restarted (cleaning Cyclos db, generating testing db) in order to refill the db
     * with Operation objects with type = TYPE_TRANSACTION_SCHEDULED
     *
     * @dataProvider providePaymentStatus 
     */
    public function testChangeScheduledTransactionStatus($newStatus)
    {
        $owner = 'labonnepioche';
        $crawler = $this->login($owner, '@@bbccdd');
        $url = '/banking/view/operations/transaction?frequency=unique';
        $crawler = $this->client->request('GET',$url);

        $ownerUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$owner));
        $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
        $accountBalanceBefore = $ownerAccount->status->balance;

        $operationRepo = $this->em->getRepository('CairnUserBundle:Operation');
        $scheduledOperations = $operationRepo->createQueryBuilder('o')
            ->where('o.fromAccountNumber = :number')
            ->andWhere('o.paymentID is not NULL')                      
            ->andWhere('o.type = :type')                               
            ->setParameter('type',Operation::TYPE_TRANSACTION_SCHEDULED)
            ->setParameter('number',$ownerAccount->number)                  
            ->getQuery()->getResult(); 

        if(empty($scheduledOperations)){
            echo 'TEST SKIPPED : INVALID DATA';
            return;
        }

        $operation = $scheduledOperations[0];

        if($newStatus == 'execute'){
            $this->assertSame(1,$crawler->filter('a[href*="'.$operation->getPaymentID().'-'.$newStatus.'"]')->count());
            $link = $crawler->filter('a[href*="'.$operation->getPaymentID().'-'.$newStatus.'"]')->eq(0)->link();
            $crawler = $this->client->click($link);

            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);
            $crawler = $this->client->followRedirect();


            $this->em->refresh($operation);
            $this->assertEquals($operation->getType(), Operation::TYPE_TRANSACTION_EXECUTED);

            $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
            $accountBalanceAfter = $ownerAccount->status->balance;

            $this->assertTrue($accountBalanceAfter < $accountBalanceBefore);
        }else{
            $paymentID = $operation->getPaymentID();
            $link = $crawler->filter('a[href*="'.$paymentID.'-'.$newStatus.'"]')->eq(0)->link();
            $crawler = $this->client->click($link);
            $form = $crawler->selectButton('confirmation_save')->form();
            $crawler =  $this->client->submit($form);
            $crawler = $this->client->followRedirect();

            $operation = $operationRepo->findOneBy(array('paymentID'=>$paymentID));
            $this->assertEquals($operation,NULL);
            $ownerAccount = $this->container->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerUser->getCyclosID())[0];
            $accountBalanceAfter = $ownerAccount->status->balance;

            $this->assertTrue($accountBalanceAfter == $accountBalanceBefore);

        }

        //committing modifications
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::commit();

        //Right after, we begin a new transaction in order to avoid the execption from PDO "there is no active transaction" which occurs
        //on rollBack (automatically called after each test by DoctrineTestBundle listener) to keep a stable state of the DB
        \DAMA\DoctrineTestBundle\Doctrine\DBAL\StaticDriver::beginTransaction();

    }

    public function providePaymentStatus()
    {
        return array(
            array('status'=>'execute'),
            array('status'=>'cancel')
        );
    }
}

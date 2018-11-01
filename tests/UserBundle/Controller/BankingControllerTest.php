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
    public function testSimpleTransaction($debitor,$creditor,$to,$expectForm,$ownsAccount,$isValid,$amount,$date)
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
            $form['cairn_userbundle_simpletransaction[date][day]']->select('1');
            $form['cairn_userbundle_simpletransaction[date][month]']->select('11');
            $form['cairn_userbundle_simpletransaction[date][year]']->select('2018');

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
        //valid data
        $baseData = array('login'=>'LaBonnePioche','creditor'=>'MaltOBar','to'=>'new','expectForm'=>true,'ownsAccount'=>true,
                          'isValid'=>true,'amount'=>'10', 'date'=>new \Datetime());

        return array(
            'unique account'=>array_replace($baseData,array('to'=>'self','expectForm'=>false)),
            'no beneficiary'=>array_replace($baseData,array('login'=>'DrDBrew', 'to'=>'beneficiary','expectForm'=>false)),
            'debitor does not own account'=>array_replace($baseData,array('ownsAccount'=>false,'isValid'=>false)),
            'not beneficiary'=>array_replace($baseData,array('to'=>'beneficiary','creditor'=>'DrDBrew','isValid'=>false)),
        );
    }

    /**
     *
     *@dataProvider provideTransactionDataForValidation
     */
    public function testTransactionValidator($frequency, $amount, $description, $fromAccount, $toAccount,$date, $isValid)
    {
        $this->container->get('cairn_user_cyclos_network_info')->switchToNetwork($this->container->getParameter('cyclos_network_cairn'));

        $validator = $this->container->get('validator');

        if($frequency == 'unique'){
            $transaction = new SimpleTransaction();
            $transaction->setDate($date); 
        }else{
            $transaction = new RecurringTransaction();
            $transaction->setFirstOccurrenceDate($date); 
            $transaction->setLastOccurrenceDate($date); 
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
            'date'=> new \Datetime(),
            'isValid'=>true );

        return array(
            'negative amount'=>array_replace($baseData,array('amount'=>'-1','isValid'=>false)),
            'undersize amount'=>array_replace($baseData,array('amount'=>'0.0099','isValid'=>false)),
            'wrong debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>array('id'=>$debitorICC + 1),'isValid'=>false)),
          'wrong creditor ICC'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>$creditorICC + 1),'isValid'=>false)),
            'wrong creditor email'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>'','email'=>'test@test.com'),
                                                                      'isValid'=>false)),
            'no creditor data'=>array_replace_recursive($baseData,array('toAccount'=>array('id'=>'','email'=>''),'isValid'=>false)),
            'no debitor ICC'=>array_replace_recursive($baseData,array('fromAccount'=>array('id'=>''),'isValid'=>false)),
            'identical accounts'=>array_replace($baseData,array(
                'toAccount'=>array('id'=>$debitorAccount->id,'email'=>$debitorUser->getEmail()),
                'isValid'=>false)),
            'insufficient balance' =>array_replace($baseData,array('amount'=>'1000000','isValid'=>false)),
            'inconsistent date'=>array_replace($baseData,array('date'=>date_modify(new \Datetime(),'+4 years'),'isValid'=>false)),
            'date before today' =>array_replace($baseData,array('date'=>date_modify(new \Datetime(),'-1 days'),'isValid'=>false)),
            'valid'=> $baseData
        );
    }


}

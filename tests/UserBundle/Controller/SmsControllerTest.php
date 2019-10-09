<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Cairn\UserBundle\Controller\SmsController;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;


use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class SmsControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

    }

    /**
     *
     *@dataProvider provideDataForSmsParsing
     */
    public function testSmsParsing($content, $isValid, $isPayment, $expectMessage)
    {
        $controller = new SmsController();
        $res = $controller->parseSms($content);
        if($isValid){
            $this->assertTrue($res->error == NULL);
            if($isPayment){
                $this->assertTrue($res->isPaymentRequest);
                $this->assertTrue(is_numeric($res->amount) );
            }
        }else{
            $this->assertTrue(strpos($res->error,$expectMessage) !== false);
        }
    }

    public function provideDataForSmsParsing()
    {
        return array(
            'invalid format : no action'=>array('12.5 SHOP', false, true, 'Envoyer PAYER, SOLDE'),
            'invalid format : no amount'=>array('PAYER SHOP',false,true,'Format du montant'),
            'invalid format : negative amount'=>array('PAYER -12.5 SHOP',false,true,'Format du montant'),
            'invalid format : no identifier'=>array('PAYER 12.5',false,true,'identifiant SMS'),
            'invalid format : action SOLDE'=>array('SOLD',false,false,'Envoyer PAYER, SOLDE'),
            'invalid format : action SOLDE + texte'=>array('SOLDE OUT!',false,false,'Demande de solde invalide'),
            'invalid format : code with some letter'=>array('12e74',false,false,'Envoyer PAYER, SOLDE'),
            'invalid format : code with two many figures'=>array('123456',false,false, '4 chiffres'),
            'invalid format : code with not enough figures'=>array('123',false,false,'Envoyer PAYER, SOLDE'),
            'valid format : uppercase'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : lowercase'=>array('payer 12.5 shop',true,true,''),
            'valid format : mix upper/lowercase'=>array('PaYer 12.5 SHoP',true,true,''),
            'valid format : action PAYE 2'=>array('PAYE 12.5 SHOP',true,true,''),
            'valid format : action PAYER 3'=>array('PAY 12.5 SHOP',true,true,''),
            'valid format : action PAYER 4'=>array('PAYEZ 12.5 SHOP',true,true,''),
            'valid format : float amount with 3 decimals'=>array('PAYER 12.522 SHOP',true,true,''),
            'valid format : float amount with 6 decimals'=>array('PAYER 12.522000 SHOP',true,true,''),
            'invalid format : float amount with dot'=>array('PAYER 12. SHOP',false,true,'Format du montant'),
            'invalid format : float amount with comma'=>array('PAYER 12, SHOP',false,true,'Format du montant'),
            'valid format : float amount with . character'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : float amount with , character'=>array('PAYER 12,5 SHOP',true,true,''),
            'valid format : amount with 1 decimal'=>array('PAYER 12.5 SHOP',true,true,''),
            'valid format : amount with 2 decimals'=>array('PAYER 12.52 SHOP',true,true,''),
            'valid format : integer amount'=>array('PAYER 12 SHOP',true,true,''),
            'valid format : integer amount with useless 0s before'=>array('PAYER 00012 SHOP',true,true,''),
            'valid format : SMS identifier starts with figures'=> array('PAYER 12.5 12SHOP',true,true,''),
            'valid format : no whitespace'=> array('PAYER12.5SHOP',true,true,''),
            'invalid format : random spaces'=> array('       PAYER12. 5    SH OP',false,true,'Format du montant'),
            'valid format : spaces b4 text'=> array('   PAYER 12.5 SHOP',true,true,''),
            'valid format : spaces after text'=> array('PAYER 12.5 SHOP   ',true,true,''),
            'valid format : multiple spaces'=> array('PAYER   12.5   SHOP   ',true,true,''),
            'valid format : SOLDE uppercase'=> array('SOLDE',true,false,''),
            'valid format : SOLDE lowercase'=> array('solde',true,false,''),
            'valid format : SOLDE mix upper/lowercase'=> array('SoLDe',true,false,''),
            'valid format : LOGIN mix upper/lowercase'=> array('LogiN',true,false,''),
            'valid format : LOGIN lowercase'=> array('login',true,false,''),
            'valid format : LOGIN uppercase'=> array('LOGIN',true,false,''),
        );
    }

     /**
     * nbEmails does not count for the code if needed. It is only the "conclusion" mails && texts
     *
     *@dataProvider provideDataForSmsOperation
     */
    public function testSmsOperation($phoneNumber, $content, $needsCode, $code, $isValidCode,$expectMessages,$nbEmails = 1)
    {
        $client = static::createClient();
        $client->enableProfiler();

        $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);

        if($expectMessages){
            //TODO : replace email by sms
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');

            if(! $needsCode){
                $this->assertEquals($nbEmails, $mailCollector->getMessageCount());
                $body = '';

                //we gather all the emails content in one
                for($i=0; $i < $nbEmails; $i++){
                    $message = $mailCollector->getMessages()[$i]->getBody();
                    $body .= $message;
                }

                //then we assert that each expected message is contained in at least one of the emails
                for($i=0; $i < $nbEmails; $i++){
                    $this->assertContains($expectMessages[$i], $body);
                }

            }else{
                $this->assertEquals(1, $mailCollector->getMessageCount());
                $this->assertContains($expectMessages[0], $mailCollector->getMessages()[0]->getBody());
                $client->enableProfiler();
                $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$code);

                if($isValidCode){
                    $mailCollector = $client->getProfile()->getCollector('swiftmailer');

                    $this->assertEquals( $nbEmails, $mailCollector->getMessageCount());

                    $body = '';
                    //we gather all the emails content in one
                    for($i=0; $i < $nbEmails; $i++){
                        $message = $mailCollector->getMessages()[$i]->getBody();
                        $body .= $message;
                    }
    
                    //then we assert that each expected message is contained in at least one of the emails
                    //First expected message is for code, so we start from one
                    for($i=1; $i < $nbEmails+1; $i++){
                        $this->assertContains($expectMessages[$i], $body);
                    }

                }
            }
        }else{
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');
            $this->assertSame(0, $mailCollector->getMessageCount());
        }

    }
  
    /**
     * nbEmails does not count for the code if needed. It is only the "conclusion" mails && texts
     */
    public function provideDataForSmsOperation()
    {
        $askCodeMsg = 'code de sécurité';
        $wrongCodeMsg = 'Échec du code';
        $validDebMsg = 'été accepté';
        $validCredMsg = 'avez reçu';
        $accessDeniedMsg = 'SMS NON AUTORISÉ';

        return array(
            'balance : phone number not registered'=>array('+33612121212','SOLDE',false,'1111',true,NULL),
            'balance : user opposed'=>array('+33744444444','SOLDE',false,'1111',true,array($accessDeniedMsg)),
            'balance : valid code + sms for pro & person'=>array('+33612345678','SOLDE',true,'1111',true,
                                                                  array($askCodeMsg,'Votre solde compte')),

            'balance : invalid code + sms for pro & person'=>array('+33612345678','SOLDE',true,'2222',false,
                                                                    array($askCodeMsg,$wrongCodeMsg)),

            'login : no pro'=>array('+33612345678','LOGIN',false,'1111',true,NULL),
            'login : pro + valid code '=>array('+33611223344','LOGIN',true,'1111',true,array($askCodeMsg,'Identifiant SMS')),
            'login : pro + wrong code '=>array('+33611223344','LOGIN',true,'2222',false,array($askCodeMsg,$wrongCodeMsg)),

            'balance : invalid sms'=>array('+33612345678','SOLD',false,'1111',true,array('SMS INVALIDE')),
            'balance : invalid sms'=>array('+33612345678','SOLDEADO',false,'1111',true,array('SMS INVALIDE')),
            'payment : wrong creditor identifier'=>array('+33612345678','PAYER12.5BOOYASHAKA',false,'1111',true,'aucun professionnel'),
            'payment mistake : person to person with ID SMS'=>array('+33612345678','PAYER10CRABEARNOLD',false,'1111',true,NULL),

            'payment : balance error'=>array('+33722222222','PAYER100MALTOBAR',false,'1111',true,
                                                                array('Solde insuffisant')),

            'payment : valid,creditor has payments disabled but reception enabled'=>array('+33612345678','PAYER10AMANSOL',false,'1111',true,
                                                                                                array($validDebMsg,$validCredMsg),2),

            'payment : debitor has sms disabled'=>array('+33733333333','PAYER100MALTOBAR',false,'1111',true,
                                                                                array($accessDeniedMsg)),

            'payment : debitor is disabled'=>array('+33744444444','PAYER100MALTOBAR',false,'1111',true,array($accessDeniedMsg)),

            'payment : debitor has payments disabled'=>array('+33655667788','PAYER100MALT',false,'1111',true,array($accessDeniedMsg)),

            'payment : creditor=debitor'=>array('+33611223344','PAYER100MALTOBAR',false,'1111',true,
                                                        array('identiques')),

            'payment : too low amount'=>array('+33612345678','PAYER0.001MALTOBAR',false,'1111',true,array('trop faible')),
            'payment : valid, no code'=>array('+33612345678','PAYER15MALTOBAR',false,'1111',true,array($validDebMsg,$validCredMsg),2),
            'payment : pro to pro,valid, no code'=>array('+33611223344','PAYER15NICOPROD',false,'1111',true,array($validDebMsg,$validCredMsg),2),
        'payment : valid + code'=>array('+33612345678','PAYER100MALTOBAR',true,'1111',true,array($askCodeMsg,$validDebMsg,$validCredMsg),2),
            'payment : person to pro,valid, no code'=>array('+33612345678','PAYER12.522maltobar',false,'1111',true,
                                                                    array($validDebMsg,$validCredMsg),2),
          'payment : valid,no code'=>array('+33612345678','PAYER12.5220000maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
            'payment : invalid sms'=>array('+33612345678','PAYER12.maltobar',false,'1111',true,array('SMS INVALIDE')),
            'payment : invalid sms'=>array('+33612345678','PAYERSHOP',false,'1111',true,array('Format du montant')),
          'payment : valid amount'=>array('+33612345678','PAYER00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAYEZ'=>array('+33612345678','PAYEZ00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAYE'=>array('+33612345678','PAYE00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),
          'payment : valid PAY'=>array('+33612345678','PAYE00012maltobar',false,'1111',true,array($validDebMsg,$validCredMsg),2),

          'payment : invalid access client'=>array('+33788888888','PAYER00012maltobar',false,'1111',true,
                                                        array('ERREUR TECHNIQUE','Accès client invalide'),2),

          'validation  : nothing to validate'=>array('+33612345678','1111',false,'1111',true,NULL),

          'suspicious payment'=>array('+33612345678','PAYER1500maltobar',false,'1111',true,array('SMS bloqués','tentative de paiement','tentative de paiement'),3),

        );
    }

    /**
     *
     *@dataProvider provideDataForSmsSpam
     */
    public function testSmsSpam($phoneNumber, $content, $spamLimit,$nbEmails, $expectMessage, $needsCode)
    {
        $client = static::createClient();
        $client->enableProfiler();

        //assert that sms is sent to user before spam limit reached
        for($i = 0; $i < $spamLimit; $i++){
            $client->enableProfiler();

            $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);
            $mailCollector = $client->getProfile()->getCollector('swiftmailer');

            $this->assertEquals($nbEmails, $mailCollector->getMessageCount());
            $this->assertContains($expectMessage, $mailCollector->getMessages()[0]->getBody());

            if($needsCode){
                $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content=1111');
            }
        }

        //then, assert that after spam limit, sms is sent to warn user about his spam activity
        $client->enableProfiler();

        $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals($nbEmails, $mailCollector->getMessageCount());
        $this->assertContains('comme du SPAM', $mailCollector->getMessages()[0]->getBody());

        //then, assert that after spam warning, nothing sent
        $client->enableProfiler();

        $crawler = $client->request('GET','/sms/reception?phone='.$phoneNumber.'&content='.$content);
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');

        $this->assertEquals(0, $mailCollector->getMessageCount());
    }

    /**
     * Only Expired spam operations are not tested here, because it would need us to change the date of received sms
     * Nevertheless, this can be easily checked manually
     *
     */
    public function provideDataForSmsSpam()
    {
        return array(
          'error : invalid access client'=>array('+33788888888','PAYER12maltobar',1,2,'ERREUR TECHNIQUE',false),
          'cancel previous operation'=>array('+33612345678','PAYER 45 MALTOBAR',4,1,'code de sécurité',false),
          'unauthorized user'=>array('+33644332211','PAYER 45 MALTOBAR',1,1,'SMS NON AUTORI',false),
          'invalid SMS format'=>array('+33612345678','SOLDEADO',4,1,'SMS INVALIDE',false),
          'invalid SMS identifier'=>array('+33612345678','PAYER 10 JOHNDOE',4,1,'aucun professionnel',false),
          'request SMS identifier'=>array('+33611223344','LOGIN',1,1,'code de sécurité',true),
          'request account balance'=>array('+33611223344','SOLDE',1,1,'code de sécurité',true),
        );
    }
}

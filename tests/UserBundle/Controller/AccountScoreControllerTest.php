<?php

namespace Tests\UserBundle\Controller;

use Tests\UserBundle\Controller\BaseControllerTest;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\AccountScore;



class AccountScoreControllerTest extends BaseControllerTest
{

    public function __construct($name = NULL, array $data = array(), $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
    }


    /**
     *@dataProvider provideAccountScoreToConfigure
     *
     */
    public function testConfigureAccountScore($current,$contractor,$isExpectedForm, $accountScoreFormat,$email,$schedule, $isNewEmail, $isValid, $expectedMessage, $nextDayToConsiderIsToday)
    {
        $crawler = $this->login($current, '@@bbccdd');

        $accountScoreRepo = $this->em->getRepository('CairnUserBundle:AccountScore');

        $currentUser = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$current));

        $contractor = $this->em->getRepository('CairnUserBundle:User')->findOneBy(array('username'=>$contractor));

        $crawler = $this->client->request('GET','/account-score/configure/'.$contractor->getUsername());

        //$crawler = $this->client->followRedirect();
        //$crawler = $this->inputCardKey($crawler,'1111');
        //$crawler = $this->client->followRedirect();

        //if(! $currentUser->hasRole('ROLE_SUPER_ADMIN')){
        //    $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
        //    return;
        //}

        if(! $isExpectedForm){
            $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
            return;
        }

        $this->client->enableProfiler();

        $form = $crawler->selectButton('cairn_userbundle_accountscore_save')->form();
        $form['cairn_userbundle_accountscore[format]']->setValue($accountScoreFormat);
        $form['cairn_userbundle_accountscore[email]']->setValue($email);

        $days = [
             'Sun',
             'Mon',
             'Tue',
             'Wed',
             'Thu',
             'Fri',
             'Sat'
         ];

        $values = $form->getPhpValues();

        foreach($days as $day){
            $count = count($schedule[$day]);
            for($cmpt = 0; $cmpt < $count ; $cmpt++ ){
                $values['cairn_userbundle_accountscore']['schedule'][$day][$cmpt] = $schedule[$day][$cmpt];
            }
        }

        $crawler = $this->client->request($form->getMethod(), $form->getUri(), $values, $form->getPhpFiles());

        $accountScore = $accountScoreRepo->findOneByUser($contractor);

        $mailCollector = $this->client->getProfile()->getCollector('swiftmailer');

        if($isValid){
            //assert that array schedules are sorted
            
            if($nextDayToConsiderIsToday){
                $this->assertEquals($accountScore->getConsideredDay(), date('D'));
            }else{
                $this->assertNotEquals($accountScore->getConsideredDay(), date('D') );
            }
            if($isNewEmail){
                //assert email is sent
                $this->assertSame(1, $mailCollector->getMessageCount());

                $message = $mailCollector->getMessages()[0];

                $this->assertInstanceOf('Swift_Message', $message);
                $this->assertContains('pointage', $message->getSubject());
                $this->assertContains($contractor->getName(), $message->getBody());
                $this->assertSame($this->container->getParameter('cairn_email_noreply'), key($message->getFrom()));
                $this->assertSame($email, key($message->getTo()));

                $this->assertNotNull($accountScore->getConfirmationToken());
                $this->assertSame(1,$crawler->filter('html:contains("'.$email.'")')->count());
                $this->assertSame(1,$crawler->filter('html:contains("activation")')->count());

                //test activation link
                $crawler = $this->client->request('GET','/account-score/confirm/'.$accountScore->getConfirmationToken());

                $this->em->refresh($accountScore);
                $this->assertNull($accountScore->getConfirmationToken());

            }else{
                $this->assertSame(0, $mailCollector->getMessageCount());

                $this->assertTrue($this->client->getResponse()->isRedirect('/account-score/view/'.$accountScore->getID()));
                $crawler = $this->client->followRedirect();

                $this->assertNull($accountScore->getConfirmationToken());
                $this->assertSame(1,$crawler->filter('html:contains("OK")')->count());
            }
        }else{
            $this->assertSame(0, $mailCollector->getMessageCount());
            $this->assertContains($expectedMessage,$this->client->getResponse()->getContent());
        }
    }


    public function provideAccountScoreToConfigure()
    {
        $admin = $this->testAdmin;

        $now = new \Datetime();
        $dayNow = $now->format('D');
        $timeNow = $now->format('H:i'); 

        $timeAfter = $now->modify('+10 min')->format('H:i');
        $timeBefore = $now->modify('-20 min')->format('H:i');

        $schedule = array('Mon'=>array('19:30'),'Tue'=>array('20:00'),'Wed'=>array(), 'Thu'=>array('1:30','23:30'), 'Fri'=>array(), 'Sat'=>array(), 'Sun'=>array());

        $scheduleAfter = $schedule;
        $scheduleBefore = $schedule;

        $scheduleAfter[$dayNow] = array($timeAfter);
        $scheduleBefore[$dayNow] = array($timeBefore);

        $baseValid = array('current'=>$admin,'target'=>'labonnepioche','expectForm'=>true,'format'=>'pdf','email'=>'labonnepioche@test.fr','schedule'=>$scheduleBefore,
                            'isNewEmail'=>false,'isValid'=>true,'expectedMessage'=>'','nextDayToConsiderIsToday'=> false);
        return array(
            'invalid: current user not referent'=>array_replace($baseValid, ['current'=> 'labonnepioche', 'target'=> 'trankilou', 'expectForm'=>false]),
            'invalid: user is not pro'=> array_replace($baseValid, ['target'=>'gjanssens', 'expectForm'=>false]),
            'invalid: admin not referent'=> array_replace($baseValid, ['target'=>'vie_integrative', 'expectForm'=>false]),
            'invalid email format' => array_replace($baseValid, ['email'=>'test@a','isNewEmail'=>true,'isValid'=>false,'expectedMessage'=>'Email invalide']),
            'invalid times : equal times same day' => array_replace($baseValid, ['schedule'=>array_replace_recursive($schedule, array('Tue'=> ['1:30','23:30','23:30']))
                                                            ,'isValid'=>false,'expectedMessage'=>'heures identiques']),

            'valid configuration : basic self made '=>array_replace($baseValid, ['current'=>'labonnepioche']),
            'valid  : admin edited an existing configuration '=>array_replace($baseValid, ['target'=>'episol','email'=>'episol@test.fr']),
            'valid  : admin edited an existing configuration, user email '=>array_replace($baseValid, ['target'=>'tout_1_fromage','email'=>'tout_1_fromage@test.fr','isNewEmail'=>false]),
            'valid  : admin edited an existing configuration, another mail '=>array_replace($baseValid, ['target'=>'tout_1_fromage','email'=>'tout_1_fromage3@test.fr','isNewEmail'=>true]),
            'valid configuration : basic made by admin '=> $baseValid,
            'valid configuration : day to consider is another day '=> array_replace( $baseValid, ['schedule' => $scheduleAfter,'nextDayToConsiderIsToday' => true ] ),
            'valid configuration : several times per day' => array_replace($baseValid, ['schedule'=>array_replace_recursive($schedule, array('Tue'=> ['1:30','23:30'], 'Fri'=> ['1:30','23:30']) )
                                ,'nextDayToConsiderIsToday' => true]),
            'valid configuration : new email'=> array_replace($baseValid, ['email'=>'labonnepioche2@test.fr','isNewEmail'=>true])
        );
    }
    
    
}

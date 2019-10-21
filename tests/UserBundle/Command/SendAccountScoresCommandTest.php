<?php

// tests/UserBundle/Command/SendAccountScoresCommandTest.php
namespace Tests\UserBundle\Command;

use Cairn\UserBundle\Command\SendAccountScoresCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Input\StringInput;

use Cairn\UserBundle\Entity\User;

use Cairn\UserBundle\Entity\AccountScore;

class SendAccountScoresCommandTest extends KernelTestCase
{

    /**
     *
     *@dataProvider provideDataForAccountScore
     */
    public function testSendAccountScoresCommand($username, $hasConfirmationToken,$format, $schedule, $isAccountScoreSent, $nbExpectedSent,$consideredDayExpected)
    {
        $kernel = static::createKernel();
        $kernel->boot();

        $container = $kernel->getContainer();
        $em = $container->get('doctrine.orm.entity_manager');
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $contractor = $userRepo->findOneByUsername($username);

        $accountScore = new AccountScore();
        $accountScore->setUser($contractor);
        $accountScore->setFormat($format);

        if($hasConfirmationToken){
            $accountScore->setConfirmationToken('aKpkv1579aBf');
        }

        $accountScore->setSchedule($schedule);

        //var_dump($accountScore->getNbSentToday());
        //var_dump($accountScore->getConsideredDay());

        $em->persist($accountScore);

        $application = new Application($kernel);
        $application->add(new SendAccountScoresCommand());

        $em->flush();

        $messageLogger = $container->get('swiftmailer.mailer.default.plugin.messagelogger');
        $messageLogger->clear();

        $command = $application->find('cairn.user:send-account-scores');
        $commandTester = new CommandTester($command);
        $commandTester->execute(array(
            'command'  => $command->getName(),
            'username' => $username
        ));

        // assert the output of the command in the console
        $output = $commandTester->getDisplay();

        if($isAccountScoreSent){
            $this->assertEquals(count($messageLogger->getMessages()),1);
            $message = $messageLogger->getMessages()[0];

            $this->assertInstanceOf('Swift_Message', $message);        
            $this->assertContains('récapitulatif', $message->getBody());
            $this->assertContains($contractor->getName(), $message->getBody());

            $this->assertSame($container->getParameter('cairn_email_noreply'), key($message->getFrom()));
            $this->assertSame($contractor->getEmail(), key($message->getTo()));

            $attachment = $message->getChildren()[0]; 

            if($format == 'csv'){
                $this->assertEquals('application/force-download', $attachment->getContentType());
            }else{//pdf
                $this->assertEquals('application/pdf', $attachment->getContentType());
            }
        }else{
            $this->assertEquals(0,count($messageLogger->getMessages()));
        }

        $this->assertEquals($accountScore->getNbSentToday(), $nbExpectedSent);
        $this->assertEquals($accountScore->getConsideredDay() ,$consideredDayExpected);

    
    }

    //$hasConfirmationToken,$format, $schedule $isAccountScoreSent, $nbExpectedSent, $expectedDay
    public function provideDataForAccountScore()
    {
        $now = new \Datetime();
        $clone = clone $now;
        $dayNow = $now->format('D');
        $timeNow = $now->format('H:i');

        $timeAfter = $now->modify('+10 min')->format('H:i');
        $timeBefore = $now->modify('-20 min')->format('H:i');

        $dayAfter = $now->modify('+1 day')->format('D');
        $dayBefore = $clone->modify('-1 day')->format('D');

        $schedule = array('Mon'=>array('1:30','23:30'),'Tue'=>array('1:30','23:30'),'Wed'=>array('1:30','23:30'), 'Thu'=>array('1:30','23:30'), 'Fri'=>array('1:30','23:30'), 
            'Sat'=>array('1:30','23:30'), 'Sun'=>array('1:30','23:30'));

        $emptySchedule = array('Mon'=>array(),'Tue'=>array(),'Wed'=>array(), 'Thu'=>array(), 'Fri'=>array(), 'Sat'=>array(), 'Sun'=>array());

        return array(
            'has confirmation token' => array('nico_faus_prod',true, 'csv',$schedule,false,1,$dayNow),
            'is not pro' => array('comblant_michel',false, 'csv',$schedule,false,1, $dayNow),
            'not considered day' => array('nico_faus_prod',false, 'csv', array_replace($schedule, [$dayNow => ['1:30','5:30'] ]),false,0, $dayAfter),
            'side effect case : one day per week' => array('nico_faus_prod',false, 'csv', array_replace($emptySchedule, [$dayNow => ['1:30','5:30'] ]),false,0,$dayAfter),
            'email to send later today' => array('nico_faus_prod',false, 'csv', $schedule,false,1, $dayNow),
        );
    }

}

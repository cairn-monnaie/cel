<?php
//src/Cairn/Tests/UserBundle/EventListener/AccountManagerTest.php

namespace Tests\UserBundle\Service;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Cairn\UserBundle\Service\AccountManager;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

use Cairn\UserBundle\Entity\Mandate;

class AccountManagerTest extends KernelTestCase {

    /**
     *
     *@dataProvider provideDataForMandates
     */
    public function testGetConsistentOperationsCount($begin, $end, $expectedCount)
    {
        $bankingService = $this->getMockBuilder('Cairn\UserCyclosBundle\Service\BankingInfo')->disableOriginalConstructor()->getMock();
        $networkService = $this->getMockBuilder('Cairn\UserCyclosBundle\Service\NetworkInfo')->disableOriginalConstructor()->getMock();
        $accountService = $this->getMockBuilder('Cairn\UserCyclosBundle\Service\AccountInfo')->disableOriginalConstructor()->getMock();
        $userService = $this->getMockBuilder('Cairn\UserCyclosBundle\Service\UserInfo')->disableOriginalConstructor()->getMock();
        $anonymous = 'anonyme';
        $bankingManager = $this->getMockBuilder('Cairn\UserCyclosBundle\Entity\BankingManager')->disableOriginalConstructor()->getMock();
        $userRepo = $this->getMockBuilder('Cairn\UserBundle\Repository\UserRepository')->disableOriginalConstructor()->getMock();

        $accountManager = new AccountManager($bankingService, $networkService, $userService,$accountService,  $anonymous, $bankingManager, $userRepo);

        $mandate = new Mandate();
        
        $begin = new \Datetime($begin);
        $end = new \Datetime($end);

        $mandate->setBeginAt($begin);
        $mandate->setEndAt($end);

        $this->assertEquals($expectedCount,$accountManager->getConsistentOperationsCount($mandate,$end) );

    }

    
    public function provideDataForMandates()
    {
        return array(
            array('begin'=>'01-09-2019','end'=>'01-05-2020','expectedCount'=>8),
            array('begin'=>'01-09-2019','end'=>'05-11-2019','expectedCount'=>2),
            array('begin'=>'17-10-2019','end'=>'29-11-2019','expectedCount'=>2),
            array('begin'=>'01-10-2019','end'=>'15-10-2019','expectedCount'=>0),
            array('begin'=>'01-11-2019','end'=>'29-11-2019','expectedCount'=>1),
            array('begin'=>'01-11-2019','end'=>'02-12-2019','expectedCount'=>1),
            array('begin'=>'15-10-2019','end'=>'02-12-2019','expectedCount'=>2),
            array('begin'=>'11-09-2019','end'=>'02-12-2019','expectedCount'=>3),
            array('begin'=>'10-02-2020','end'=>'28-02-2020','expectedCount'=>1),
            array('begin'=>'01-02-2020','end'=>'01-03-2020','expectedCount'=>1),
            array('begin'=>'01-02-2020','end'=>'29-02-2020','expectedCount'=>1),
            array('begin'=>'17-02-2020','end'=>'01-03-2020','expectedCount'=>1),
            array('begin'=>'17-02-2020','end'=>'15-03-2020','expectedCount'=>1),
            array('begin'=>'10-02-2020','end'=>'15-04-2020','expectedCount'=>2),
            array('begin'=>'10-02-2020','end'=>'28-02-2020','expectedCount'=>1),
            array('begin'=>'10-11-2019','end'=>'05-02-2020','expectedCount'=>3),
        );

    }

}

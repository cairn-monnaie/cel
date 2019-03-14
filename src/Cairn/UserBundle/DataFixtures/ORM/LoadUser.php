<?php
// src/Cairn/UserBundle/DataFixtures/ORM/LoadCategory.php

namespace Cairn\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Address;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserCyclosBundle\Entity\UserManager;

class LoadUser implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
        $baseData = array('username'=>'testUser','name'=>'Test User');

        for($i = 5; $i < 12; $i++){
            $user = new User();
            $user->setName($baseData['name'] . $i);                                        
            $user->setUsername($baseData['username'] . $i);                            
            $user->setEmail('testuser'.$i.'@test.com');                            
            $user->setCyclosID($i);
            $user->setMainICC(null);
            $user->setPlainPassword(User::randomPassword());                            
            $user->setEnabled(true);                            
            $user->addRole('ROLE_PRO');

            $zip = $manager->getRepository('CairnUserBundle:ZipCity')->findOneBy(array('zipCode'=>'38000','city'=>'Grenoble'));
            $address = new Address();
            $address->setZipCity($zip);
            $address->setStreet1('7 rue Très Cloîtres');

            $user->setAddress($address);
            $user->setDescription('test for command');

            //set the interesting data for testing card validation command
            $card = new Card($user,5,5,'@@@@');
            $card->setCreationDate(date_modify(new \Datetime(),'- '.$i.' days'));
            $card->setGenerated(true);
            $card->setEnabled(false);
            $user->setCard($card);

            $manager->persist($user);
        }

        $manager->flush();
    }
}

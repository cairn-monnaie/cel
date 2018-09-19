<?php
// src/Cairn/UserCyclosBundle/DataFixtures/ORM/LoadUser.php

namespace Cairn\UserCyclosBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Cairn\UserCyclosBundle\Entity\User;

class LoadUser implements FixtureInterface
{
    public function load(ObjectManager $manager)
    {
        // Les noms d'utilisateurs à créer
        $listNames = array('Alexandre', 'Marine', 'Anna');

        foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User();

            // Le nom d'utilisateur et le mot de passe sont identiques pour l'instant
            $user->setUsername($name);
            $user->setName($name);
            $user->setEmail($name . '@cairn-monnaie.com');
            $user->setEnabled(true);
            $user->setPassword($name);

            // On ne se sert pas du sel pour l'instant
            $user->setSalt('');
            // On définit uniquement le role ROLE_PRO qui est le role de base
            $user->setRoles(array('ROLE_PRO'));

            // On le persiste
            $manager->persist($user);
        }

        //on définit un super_admin et un ExOFf
        $user1 = new User();
        $user2 = new User();

        $name1 = 'Simon'; $name2 = 'Maxime';

        $user1->setUsername($name1);
        $user1->setName($name1);
        $user1->setEmail($name1 . 'cairn-monnaie.com');
        $user1->setEnabled(true);
        $user1->setPassword($name1);

        $user2->setUsername($name2);
        $user2->setName($name2);
        $user2->setEmail($name2 . 'cairn-monnaie.com');
        $user2->setEnabled(true);
        $user2->setPassword($name2);

        // On ne se sert pas du sel pour l'instant
        $user1->setSalt('');
        $user2->setSalt('');

        // On définit uniquement le role ROLE_PRO qui est le role de base
        $user1->setRoles(array('ROLE_ExOFF'));
        $user2->setRoles(array('ROLE_SUPER_ADMIN'));

        // On le persiste
        $manager->persist($user1);
        $manager->persist($user2);

        // On déclenche l'enregistrement
        $manager->flush();
    }
}

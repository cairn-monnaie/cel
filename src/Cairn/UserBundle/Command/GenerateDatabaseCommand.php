<?php

// src/Cairn/UserBundle/Command/GenerateDatabaseCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class GenerateDatabaseCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:generate-database')
            ->setDescription('Generates a database from scratch, based on a Cyclos database')
            ->setHelp('This command requires the login and password of an administrator on Cyclos-side in order to connect and access users\' data. Then, we replicate those data into doctrine entities.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username of a cyclos admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Password of the same cyclos admin')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $message = $commandsService->generateDatabaseFromCyclos($username, $password);
        $output->writeln($message);
    }
}

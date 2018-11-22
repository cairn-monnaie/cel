<?php

// src/Cairn/UserBundle/Command/CreateInstallAdminCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class CreateInstallAdminCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:create-install-admin')
            ->setDescription('Creates the install admin')
            ->setHelp('This command installs the installed network admin if it does not exist.')
            ->addArgument('username', InputArgument::REQUIRED, 'Username of installed network admin')
            ->addArgument('password', InputArgument::REQUIRED, 'Password of installed network admin')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $username = $input->getArgument('username');
        $password = $input->getArgument('password');

        $message = $commandsService->createInstallAdmin($username, $password);
        $output->writeln($message);
    }
}

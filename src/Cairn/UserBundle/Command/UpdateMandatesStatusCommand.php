<?php

// src/Cairn/UserBundle/Command/UpdateMandatesStatusCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class UpdateMandatesStatusCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:update-mandates-status')
            ->setDescription('Updates status of ongoing and scheduled mandates')
            ->setHelp('This command can contain one optional argument : an username to specify one user')
            ->addArgument('username', InputArgument::OPTIONAL, 'Username of the user whom you want the mandate updated')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $username = $input->getArgument('username');

        $message = $commandsService->updateMandatesStatusCommand($username);
        $output->writeln($message);
    }
}

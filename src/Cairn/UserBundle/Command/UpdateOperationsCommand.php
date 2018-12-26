<?php

// src/Cairn/UserBundle/Command/UpdateOperationsCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Cairn\UserBundle\Service\Commands;

class UpdateOperationsCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:update-operations-status')
            ->setDescription('Changes the status of banking operations to be consistent with Cyclos database')
            ->setHelp('This command looks for all operations that have been initiated but not completed and removes them from the database. Moreover, some scheduled operations may be executed on Cyclos-side everyday, so this command follows these scheduled payments and updates their status to EXECUTED/SCHEDULED/FAILED.')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');
        $commandsService->updateOperations();
        $output->writeln('The status of operations has been updated successfully !');
    }
}

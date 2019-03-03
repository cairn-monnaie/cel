<?php

// src/Cairn/UserBundle/Command/RemoveAbortedOperationsCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Cairn\UserBundle\Service\Commands;

class RemoveAbortedOperationsCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:remove-aborted-operations')
            ->setDescription('Changes the status of banking operations to be consistent with Cyclos database')
            ->setHelp('This command looks for all operations that have been initiated but not completed and removes them from the database.            ')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');
        $commandsService->removeAbortedOperations();
        $output->writeln('Transaction operations and expired SMS have been updated successfully !');
    }
}

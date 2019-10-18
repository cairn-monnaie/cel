<?php

// src/Cairn/UserBundle/Command/SendAccountScoresCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class SendAccountScoresCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:send-account-scores')
            ->setDescription('Send a reconciliation account document')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $message = $commandsService->sendAccountScores();
        $output->writeln($message);
    }
}

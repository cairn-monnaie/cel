<?php

// src/Cairn/UserBundle/Command/CheckCardsValidationCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Cairn\UserBundle\Service\Commands;

class CheckCardsValidationCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:check-cards-validation')
            ->setDescription('Checks creation date of new cards and remove them if expiry date is reached')
            ->setHelp('This command checks creation date of new cards and warn them that they need to activate it before expiration date. Otherwise, it is automatically removed with an email notification, so they will need to order a new card again.')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');
        $commandsService->checkCardsActivation();
        $output->writeln('Checking of cards activation performed successfully!');
    }
}

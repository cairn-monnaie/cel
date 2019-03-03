<?php

// src/Cairn/UserBundle/Command/CheckCardsAssociationCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Cairn\UserBundle\Service\Commands;

class CheckCardsAssociationCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:check-cards-association')
            ->setDescription('Checks creation date of unassociated cards and remove them if expiry date is reached')
            ->setHelp('This command checks creation date of cards which have no user associated and automatically removes them if expiry date has been passed over.')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');
        $commandsService->checkCardsAssociation();
        $output->writeln('Checking of cards association performed successfully!');
    }
}

<?php

// src/Cairn/UserBundle/Command/CheckEmailsConfirmationCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

use Cairn\UserBundle\Service\Commands;

class CheckEmailsConfirmationCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this->setName('cairn.user:check-emails-confirmation')
            ->setDescription('Checks creation date of new users and remove them if expiry date is reached')
            ->setHelp('This command checks creation date of new users and warn them that they need to activate their member area before expiration date. Otherwise, it is automatically removed with an email notification')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');
        $commandsService->checkEmailsValidation();
        $output->writeln('Checking of emails validation performed!');
    }
}

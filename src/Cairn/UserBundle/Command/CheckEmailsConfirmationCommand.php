<?php

// src/Cairn/UserBundle/Command/CheckEmailsConfirmationCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CheckEmailsConfirmationCommand extends Command
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
        // outputs multiple lines to the console (adding "\n" at the end of each line)
        $output->writeln([
            'User Creator',
            '============',
            '',
        ]);
    }
}

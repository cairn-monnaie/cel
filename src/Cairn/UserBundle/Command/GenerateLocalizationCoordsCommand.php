<?php

// src/Cairn/UserBundle/Command/GenerateLocalizationCoordsCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class GenerateLocalizationCoordsCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:generate-localization-coords')
            ->setDescription('Generates latitude and longitude according to user\'s address')
            ->setHelp('If no input is provided, the coordinates are generated for all adherents')
            ->addArgument('username', InputArgument::OPTIONAL, 'Username of the adherent to get localization coordinates')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $username = $input->getArgument('username');
        $message = $commandsService->generateLocalizationCoordinates($username);

        $output->writeln($message);
    }
}

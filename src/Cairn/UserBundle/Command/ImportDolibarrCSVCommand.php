<?php

// src/Cairn/UserBundle/Command/ImportDolibarrCSVCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;

use Cairn\UserBundle\Service\Commands;

class ImportDolibarrCSVCommand extends ContainerAwareCommand
{


    protected function configure()
    {
        $this->setName('cairn.user:import-dolibarr-one-shot')
            ->setDescription('Import Pro data based on dolibarr CSV export. The dolibarr export must have been generated using the predefined export profile "export-pro-map-ecairn". Be careful, possible duplicates...')
            ->addArgument('filePath', InputArgument::REQUIRED, 'Path to the CSV exported file')
            ; 
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $commandsService = $this->getContainer()->get('cairn_user.commands');

        $path = $input->getArgument('filePath');
        $message = $commandsService->importDolibarrPros($path);

        $output->writeln($message);
    }
}

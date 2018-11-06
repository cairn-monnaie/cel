<?php
// src/Cairn/UserBundle/Command/OAuthAddClientCommand.php
namespace Cairn\UserBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class OAuthAddClientCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('oauth:add-client')
            ->setDescription("Ads a new client for OAuth")
            ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $redirectUri = $this->getContainer()->getParameter('router.request_context.scheme') . "://" . $this->getContainer()->getParameter('router.request_context.host');
        $clientManager = $this->getContainer()->get('fos_oauth_server.client_manager.default');
        $client = $clientManager->createClient();
        $client->setRedirectUris(array($redirectUri));
        $client->setAllowedGrantTypes(array('refresh_token', 'password'));
        $clientManager->updateClient($client);
    }
}

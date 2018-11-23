<?php
// src/Cairn/UserCyclosBundle/Service/NetworkInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 *This class contains getters related to networks in Cyclos
 *                                                                             
 */
class NetworkInfo
{

    /**                                                                        
     * Deals with all accounts management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\AccountService $accountService                                            
     */
    private $networkService;

    /**
     * Root of the URL to reach the Cyclos platform in production mode. Set as a global parameter
     *
     *@var string $cyclosRootProdUrl      
     */
    private $cyclosRootProdUrl;

    /**
     * Root of the URL to reach the Cyclos platform in test mode. Set as a global parameter
     *
     *@var string $cyclosRootTestUrl      
     */
    private $cyclosRootTestUrl;

    /**
     * Root of the URL to reach the Cyclos platform in current mode
     *
     *@var string $currentRootUrl      
     */
    private $currentRootUrl;

    /**
     * Current environment mode
     *
     *@var string $environment
     */
    private $environment;

    public function __construct($cyclosRootProdUrl,$cyclosRootTestUrl,$environment)
    {
        $this->networkService = new Cyclos\NetworkService();
        $this->cyclosRootProdUrl = $cyclosRootProdUrl;
        $this->cyclosRootTestUrl = $cyclosRootTestUrl;
        $this->environment = $environment;

        if($this->environment == 'test' ){
            $this->currentRootUrl = $this->cyclosRootTestUrl;
        }else{
            $this->currentRootUrl = $this->cyclosRootProdUrl;
        }
    }

    /**
     * Provides ID of network $name
     *
     * @param string $name
     * @return int 
     */
    public function getNetworkID($name)
    {
        Cyclos\Configuration::setRootUrl($this->currentRootUrl.'/global');       
        $query = new \stdClass();
        $query->name = $name;
        $networks = $this->networkService->search($query)->pageItems;

        if(sizeof($networks) == 0){
            return NULL;
        }
        elseif(sizeof($networks) >= 2){
            foreach($networks as $network){
                if($network->name == $name){
                    return $network->id;
                }
            }
            return NULL;
        }
        return $networks[0]->id;
    }

    /*
     * get network data
     *@param string $name
     *@return D
     */
    public function getNetworkData($name)
    {
        return $this->networkService->getData($this->getNetworkID($name));
    }

    /*
     * get network DTO
     * @param string $name
     * @return DTO
     */
    public function getNetworkDTO($name)
    {
        return $this->networkService->load($this->getNetworkID($name));
    }

    /**
     * Returns a list of networks
     *
     * @param bool $enabled
     * @return array of stdClass representing org.cyclos.model.system.networks.NetworkVO
     */
    public function getListNetworks($enabled)
    {
        $query = new \stdClass();
        $query->managedByGroups = new \stdClass();
        $query->managedByGroups->nature = 'ADMIN_GROUP';
        $query->enabled = $enabled;
        return $this->networkService->search($query)->pageItems;
    }

    /**
     * Switches the user to the network $name
     *
     * This function is important for the user to connect to Cyclos and access web services
     * To do so, we edit the cyclos configuration
     *
     * @param string $name Name of the network | global administration
     */
    public function switchToNetwork($internalName, $method, $credentials)
    {
        if($internalName == 'global'){
            throw new AccessDeniedException('Vous n\'avez pas accès à cette section');
        }

        if($method == 'login'){
            Cyclos\Configuration::setAuthentication($credentials['username'],$credentials['password']); 
        }elseif($method == 'session_token'){
             Cyclos\Configuration::setSessionToken($credentials); 
        }elseif($method == 'access_client'){
             Cyclos\Configuration::setAccessClientToken($credentials); 
        }

        Cyclos\Configuration::setRootUrl($this->currentRootUrl .'/'. $internalName);
    }
}

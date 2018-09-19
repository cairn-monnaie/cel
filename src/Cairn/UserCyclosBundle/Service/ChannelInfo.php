<?php
// src/Cairn/UserCyclosBundle/Service/ChannelInfo.php

namespace Cairn\UserCyclosBundle\Service;

//manage Cyclos configuration file                                             
use Cyclos;

/**
 *This class contains getters related to channel objects
 *                                                                             
 */
class ChannelInfo
{

    /**                                                                        
     * Deals with all channels management actions to operate.
     *
     * This attribute is an instance of a Cyclos Service class proposed in the Cyclos WebServices PHP API.
     *@var Cyclos\AccountService $accountService                                            
     */
    private $channelService;

    public function __construct()
    {
        $this->channelService = new Cyclos\ChannelService();
    }

    /**
     * Get all channels
     *
     *@return stdClass representing Java type: java.util.List of org.cyclos.model.access.channels.ChannelVO
     */
    public function getListChannels()
    {
        return $this->channelService->_list();
    }

    /**
     * Get a channel by internal name
     *
     * @return stdClass representing Java type: org.cyclos.model.access.channels.ChannelVO
     */
    public function getChannelVO($internalName)
    {
        $list = $this->getListChannels();
        foreach($list as $channel)
        {
            if($channel->internalName == $internalName){
                return $channel;
            }
        }
        return NULL;
    }
}

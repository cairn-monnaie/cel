<?php
namespace Cairn\UserCyclosBundle\Entity;

//manage Cyclos configuration file                                             
use Cyclos;

class ScriptManager
{
    private $scriptService;

    public function __construct()
    {
        $this->csService = new Cyclos\CustomScriptService();

    }


    public function runScript($scriptName){
        $scriptParams = new \stdClass();
        $scriptParams->script = $scriptName;
        $scriptParams->runAsSystem = true;
        return $this->csService->run($scriptParams);
    }


}

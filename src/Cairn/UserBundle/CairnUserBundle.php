<?php

namespace Cairn\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CairnUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }

}

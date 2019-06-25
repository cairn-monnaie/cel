<?php
// src/CairnUserBundle/Form/Extension/DisableCSRFExtension.php

namespace Cairn\UserBundle\Form\Extension;

use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Service\Api;

class DisableCSRFExtension extends AbstractTypeExtension
{
    protected $apiService;

    public function __construct(Api $apiService)
    {
        $this->apiService = $apiService;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        if($this->apiService->isRemoteCall()){
            $resolver->setDefaults(array(
                'csrf_protection'=>false,
            ));
        }else{
            return;
        }

    }

    public function getExtendedType()
    {
        return FormType::class; 
    }
}

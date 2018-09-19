<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\TransferType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ConversionType extends TransferType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('fromAccount');
//            $builder->get('date')->setData('property',new \DateTime());
        $builder->remove('date');
    }


    public function getParent()
    {
         return TransferType::class;
    }       

}

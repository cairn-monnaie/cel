<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\TransferType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DepositType extends TransferType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('fromAccount')
            ->remove('description')
            ->remove('date');
    }


    public function getParent()
    {
        return TransferType::class;
    }       

}

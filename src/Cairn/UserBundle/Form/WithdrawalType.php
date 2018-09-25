<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\TransactionType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;


class WithdrawalType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('toAccount')
            ->remove('description');
    }

    public function getParent()
    {
        return TransactionType::class;
    }       

}

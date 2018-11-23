<?php

namespace Cairn\UserBundle\Form;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\AbstractType;
use Cairn\UserBundle\Form\SimpleTransactionType;

class ConversionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('fromAccount');
//            $builder->get('date')->setData('property',new \DateTime());
        $builder->remove('date');
    }


    public function getParent()
    {
         return SimpleTransactionType::class;
    }       

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_conversion';
    }

}

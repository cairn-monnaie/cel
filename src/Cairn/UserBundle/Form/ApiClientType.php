<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class ApiClientType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('login', TextType::class, array('label'=> 'Login API'))
            ->add('accessToken', TextType::class, array('label'=>'Nouvelle clé API','required'=>false))
            ->add('webhook', TextType::class,array('label'=>'Point de rappel','required'=>false))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation'));


    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\ApiClient'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_apiclient';
    }


}

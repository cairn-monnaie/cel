<?php
// src/CairnUserBundle/Form/ChangePasswordType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('plainPassword', RepeatedType::class, array(                 
                'type' => PasswordType::class,                             
                'options' => array(                                        
                    'translation_domain' => 'FOSUserBundle',               
                    'attr' => array(                                       
                        'autocomplete' => 'new-password',                  
                    ),                                                     
                ),                                                         
                'first_options' => array('label' => 'Nouveau mot de passe'),
                'second_options' => array('label' => 'Confirmation'),      
                'invalid_message' => 'Les champs ne correspondent pas',    
            ))                                                             
            ->add('save',SubmitType::class,array('label'=>'Valider'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\User'
        ));
    }

    public function getParent()
    {
        return CurrentPasswordType::class;
    }

}

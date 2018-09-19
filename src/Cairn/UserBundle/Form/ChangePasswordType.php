<?php
// src/CairnUserBundle/Form/ChangePasswordType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\FormBuilderInterface;

class ChangePasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class,array(
                'label'=>'Mot de passe actuel',
                'mapped'=>false))
            ->add('plainPassword', RepeatedType::class, array(                 
                'mapped'=>false,
                'constraints'=>array(                                          
                    new Assert\Length(array(                                   
                        'min'=>8,                                              
                        'minMessage'=>'Au moins 8 caractères',                 
                        'max'=>12,                                             
                        'maxMessage'=>'Au maximum 12 caractères'))             
                    ),                                                         
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


}

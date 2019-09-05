<?php
// src/CairnUserBundle/Form/ChangePasswordType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Entity\User;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Security\Core\Encoder\UserPasswordEncoder;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ChangePasswordType extends AbstractType
{
    private $passwordEncoder;

    public function __construct(UserPasswordEncoder $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

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
                'first_options' => array('label' => 'Nouveau mot de passe ( 8 - 25 caractères + 1 caractère spécial )'),
                'second_options' => array('label' => 'Confirmation'),      
                'invalid_message' => 'Les champs ne correspondent pas',    
            ));
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }
                $newPassword = $form->get('plainPassword')->getData();
                if($this->passwordEncoder->isPasswordValid($user, $newPassword)){
                    $error = new FormError('Ce mot de passe est déjà utilisé');
                    $error->setOrigin($form->get('plainPassword'));
                    $form->addError($error);
                }
            }
        );

        $builder->add('save',SubmitType::class,array('label'=>'Valider'));
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

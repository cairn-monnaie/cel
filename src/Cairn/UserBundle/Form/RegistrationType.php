<?php
// src/CairnUserBundle/Form/RegistrationType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Repository\UserRepository;

use Cairn\UserBundle\Form\AddressType;
use Cairn\UserBundle\Form\ImageType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Security;

class RegistrationType extends AbstractType
{
    private $authorizationChecker;

    public function __construct(AuthorizationChecker $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('name', TextType::class,array('label'=>'Nom de la structure'));
        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
            $builder->remove('plainPassword');
        }else{
            $builder->add('plainPassword', RepeatedType::class, array(
                'constraints'=>array(
                    new Assert\Length(array(
                        'min'=>8,
                        'minMessage'=>'Au moins 8 caractères',
                        'max'=>25,
                        'maxMessage'=>'Au maximum 25 caractères'))
                    ),
                    'type' => PasswordType::class,
                    'options' => array(
                        'translation_domain' => 'FOSUserBundle',
                        'attr' => array(
                            'autocomplete' => 'new-password',
                        ),
                    ),
                    'first_options' => array('label' => 'form.password'),
                    'second_options' => array('label' => 'form.password_confirmation'),
                    'invalid_message' => 'fos_user.password.mismatch',
                ));
        }
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }

                if($user->hasRole('ROLE_PRO')){
                    if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                        $form->add('singleReferent', EntityType::class, array(                       
//                            'constraints'=>array(
//                                new Assert\NotNull()
//                            ),
                            'label'=>'Groupe local référent',
                            'class'=> User::class,                                         
                            'choice_label'=>'name',                                        
                            'query_builder' => function (UserRepository $ur) {             
                                $ub = $ur->createQueryBuilder('u');                        
                                $ur->whereRole($ub,'ROLE_ADMIN');                          
                                return $ub;                                                
                            },                                             
                            'expanded'=>true,
                            'required'=>false
                        ));
                    }
                }
            }
        );
        $builder->add('address', AddressType::class)
            ->add('description',TextareaType::class)
            ->add('image', ImageType::class,array('required'=>false));

    }


    public function getParent()
    {
        return 'FOS\UserBundle\Form\Type\RegistrationFormType';
    }

    public function getBlockPrefix()
    {
        return 'app_user_registration';
    }

    // For Symfony 2.x
    public function getName()
    {
        return $this->getBlockPrefix();
    }
}

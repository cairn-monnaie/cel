<?php
// src/CairnUserBundle/Form/RegistrationType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Repository\UserRepository;

use Cairn\UserBundle\Form\AddressType;
use Cairn\UserBundle\Form\ImageType;
use Cairn\UserBundle\Form\IdentityDocumentType;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
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
    private $em;

    public function __construct(AuthorizationChecker $authorizationChecker, EntityManager $em)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('username');
        $builder->remove('plainPassword');
        $builder->add('address', AddressType::class);

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }

                $label = ($user->hasRole('ROLE_PRO')) ? 'Justificatif d\'activité professionnelle' :'Pièce d\'identité';
                $form->add('identityDocument', IdentityDocumentType::class,
                    array(
                        'label'=>$label,
                        'attr' => array('class'=>'identity-document')
                    ));

                if($user->hasRole('ROLE_PRO')){
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure'));
                    //$form->add('image', ImageType::class,array('label'=>'Logo'));
                    $form->add('description',TextareaType::class,array('label'=>'Décrivez ici votre activité en quelques mots ...'));
                    if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                        $form->add('singleReferent', EntityType::class, array(
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

                    $form->add('image', ImageType::class,array('label'=>'Votre logo'));

                }elseif($user->hasRole('ROLE_PERSON')){
                    $form->add('name', TextType::class,array('label'=>'Votre nom'));
                    $form->add('firstname', TextType::class,array('label'=>'Votre prénom'));
                    $form->add('description',TextareaType::class,array('label'=>
                                    'Décrivez ici en quelques mots pourquoi vous utilisez le Cairn :) '));
                }else{
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure admin'));
                    $form->add('description',TextareaType::class,array('label'=>
                                    'Décrivez ici en quelques mots son rôle au sein du Cairn :) '));

                }
            }
        );
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $user = $event->getData();
                if(null === $user){
                    return;
                }
                $user->setUsername($this->generateUsername($user));
            }
        );
        $builder->add('address', AddressType::class);
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

    private function generateUsername(User $user)
    {
        if (!$user->getName()) {
            return null;
        }

        $username = User::makeUsername($user->getName(),$user->getFirstname());
        $qb = $this->em->createQueryBuilder();
        $usernames = $qb->select('u')->from('CairnUserBundle:User', 'u')
            ->where($qb->expr()->like('u.username', $qb->expr()->literal($username . '%')))
            ->orderBy('u.username', 'DESC')
            ->getQuery()
            ->getResult();

        if (count($usernames)) {
            if (count($usernames)==1 && $usernames[0]->hasRole('ROLE_PERSON') && $user->hasRole('ROLE_PRO')){
                //if only one exist and is the part version of the pro we want create
                $username = $username.'_pro';
            }else{
                $count = 1;
                $first = $usernames[0]->getUsername();
                if(preg_match_all('/\d+/', $first, $numbers)) {
                    $count = end($numbers[0]) + 1;
                }
                $username = $username . + $count;
            }
        }
        return $username;
    }
}

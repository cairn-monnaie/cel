<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Validator\UserPassword;

use FOS\UserBundle\Form\Type\ProfileFormType;
use Cairn\UserBundle\Form\AddressType;
use Cairn\UserBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class ProfileType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->remove('username');
        $builder->add('username',TextType::class,array('label'=>'Nom d\'utilisateur','disabled'=>true));
        $builder
            ->add('current_password', PasswordType::class,array(
                'label'=>'Mot de passe actuel',
                'mapped'=>false,
                'constraints'=>new UserPassword() ))
            ->add('address' , AddressType::class);
        $builder->addEventListener(
            FormEvents::SUBMIT,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }
                $user->setPlainPassword($form->get('current_password')->getData());
            }
        );
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }
                if($user->hasRole('ROLE_PRO')){
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure','disabled'=>true));
                    //$form->add('image', ImageType::class,array('label'=>'Logo'));
                    $form->add('description',TextareaType::class,array('label'=>'Décrivez ici votre activité en quelques mots ...'));
                    if ($user->getImage())
                        $form->add('image', ImageType::class,array('label'=>'Changer votre logo'));
                    else
                        $form->add('image', ImageType::class,array('label'=>'Votre logo'));

                }elseif($user->hasRole('ROLE_PERSON')){
                    $form->add('name', TextType::class,array('label'=>'Votre nom et prénom','disabled'=>true));
                    $form->add('description',TextareaType::class,array('label'=>
                        'Décrivez ici en quelques mots pourquoi vous utilisez le Cairn :) '));
                }else{
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure admin','disabled'=>true));
                    $form->add('description',TextareaType::class,array('label'=>
                        'Décrivez ici en quelques mots son rôle au sein du Cairn :) '));

                }
            }
        );
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
        //        return 'FOS\UserBundle\Form\Type\ProfileFormType';
        return ProfileFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_user_profile_edit';
    }


}

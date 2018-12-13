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
        $builder
            ->add('current_password', PasswordType::class,array(
                'label'=>'Mot de passe actuel',
                'mapped'=>false,
                'constraints'=>new UserPassword() ))
            ->add('description', TextareaType::class)
            ->add('address' , AddressType::class)
            ->add('image', ImageType::class);
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
        $builder->add('save' , SubmitType::class);
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

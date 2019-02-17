<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Validator\UserPhoneNumber;

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

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

class PhoneNumberType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('phoneNumber', TextType::class,array(
                'label'=>'Numéro de téléphone portable',
                'constraints'=>new UserPhoneNumber() 
            ))
            ->add('save' , SubmitType::class);
    }

//    /**
//     * {@inheritdoc}
//     */
//    public function configureOptions(OptionsResolver $resolver)
//    {
//        $resolver->setDefaults(array(
//            'data_class' => 'Cairn\UserBundle\Entity\User'
//        ));
//    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_user_phone_number_edit';
    }


}

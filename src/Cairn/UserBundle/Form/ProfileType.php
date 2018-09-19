<?php

namespace Cairn\UserBundle\Form;

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
                ->add('description', TextareaType::class)
                ->add('address' , AddressType::class)
                ->add('image', ImageType::class,array('required'=>false));
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }

                if($user->hasRole('ROLE_PRO')){
                    $form->add('rib', TextType::class, array('label' => 'Votre RIB',
                        'constraints'=> array(
                            new Assert\Length(array(
                                'min'=>23,
                                'exactMessage'=>'Un RIB est composé de 23 caractères')),
                            new Assert\Regex(array(
                                'pattern'=>"#^[0-9]{10}[A-Z0-9]{11}[0-9]{2}$#",
                                'message'=>'Format invalide'))
                            )
                        ));    

                }
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
        return 'app_user_profile_edit';
    }


}

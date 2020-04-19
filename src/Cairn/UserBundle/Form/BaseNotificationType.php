<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\BaseNotification;
use Cairn\UserBundle\Entity\PaymentNotification;
use Cairn\UserBundle\Entity\RegistrationNotification;

use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class BaseNotificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('webPushEnabled', CheckboxType::class,array('required'=>false))
            ->add('appPushEnabled', CheckboxType::class,array('required'=>false))
            ->add('emailEnabled', CheckboxType::class,array('required'=>false))
            ->add('smsEnabled', CheckboxType::class,array('required'=>false));

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                $baseNotif = $event->getData();
                $form = $event->getForm();
                if(null === $baseNotif){
                    return;
                }

                if($baseNotif instanceof PaymentNotification){
                    $isPro = $baseNotif->getNotificationData()->getUser()->hasRole('ROLE_PRO');
                    $form->add('types', ChoiceType::class, array(
                        'choices' => Operation::getNotifTypes($isPro),
                        'choice_label'=>function($type){
                            return Operation::getTypeName($type);
                        },
                        'multiple' => true,
                        'required' => false
                    ))
                    ->add('minAmount', IntegerType::class,array('label'=>'Montant minimum'));
                    
                }else{
                    $form->add('radius', RangeType::class,array('label'=>'Dans un rayon(km)','attr'=>array('min'=>'0')));
                }
               
            }
        );

    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\BaseNotification'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_basenotification';
    }


}

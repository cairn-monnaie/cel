<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Cairn\UserBundle\Entity\Operation;

use NotificationPushType;

class PaymentPushNotificationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('types', ChoiceType::class, array(
                     'choices' => Operation::ARRAY_TRANSFER_TYPES,
                     //'choice_value' =>
                     //    function($type) {
                     //        var_dump($type);
                     //        return Operation::getTypeIndex($type);
                     //    },
                     'multiple' => true,
                     'required' => false
            ))
            ->add('minAmount', IntegerType::class);

        $builder->addEventListener( // change options depending on if mandate is created or edited
            FormEvents::PRE_SUBMIT,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                if(null === $data){
                    return;
                }

               
                $types = $data['types'];
                foreach($types as $key=>$type){
                    $data['types'][$key] = Operation::getTypeIndex($type);
                }
                $event->setData($data);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\PaymentPushNotification'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_paymentpushnotification';
    }

    public function getParent()
    {
        return PushNotificationType::class;
    }

}

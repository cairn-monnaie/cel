<?php

namespace Cairn\UserCyclosBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class TransactionCategoryType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name'    ,ChoiceType::class,array('label' => 'nom',
                'choices' => array(
                    'dépôt sur le compte'=>'credit',
                    'retrait du compte' =>'debit',
                    'reconversion'=>'conversion',
                    'virement'    => 'transfer'
                )));

        $formModifier = function (FormInterface $form, $name = null) {
            if($name == 'credit'){
                $form->add('nbCairns',IntegerType::class,array('label' => 'Quantité de cairns'))
                    ->add('nbEuros' ,IntegerType::class,array('label' => 'Quantité d\'euros échangés'));
            }
        };

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($formModifier) {
                // this would be your entity, i.e. SportMeetup
                $data = $event->getData();

                $formModifier($event->getForm(), $data['name']);
            }
        );

        $builder->get('name')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $name = $event->getForm()->getData();

                $formModifier($event->getForm()->getParent(), $name);
            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserCyclosBundle\Entity\TransactionCategory',
            'attr'         => ['id' => 'type_form'] 
        ));
   }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_usercyclosbundle_transactioncategory';
    }


}

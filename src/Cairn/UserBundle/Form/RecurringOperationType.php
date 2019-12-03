<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Entity\Operation;

class RecurringOperationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $periodicities = array('mensuelle'=>'1','bimestrielle'=>'2','trimestrielle'=>'3','semestrielle'=>'6');

        $builder
            ->add('firstOccurrenceDate', DateType::class , array(
                    'mapped'=>false,
                    'label'=> 'Date du premier virement',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    "attr"=>array('class'=>'datepicker_cairn')))

            ->add('lastOccurrenceDate', DateType::class , array(
                    'data'=> new \Datetime('now +2 months'),
                    'mapped'=>false,
                    'label'=> 'Date de dernière occurrence',
                    'widget' => 'single_text',
                    'format' => 'yyyy-MM-dd',
                    "attr"=>array('class'=>'datepicker_cairn')))
            ->add('periodicity', ChoiceType::class, array(
                    'mapped'=>false,
                    'label' => 'Périodicité',
                    'choices' => $periodicities
                    ));

        
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Operation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_simpleoperation';
    }

    public function getParent()
    {
        return OperationType::class;
    }

}



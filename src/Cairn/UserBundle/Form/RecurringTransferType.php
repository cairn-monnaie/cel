<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RecurringTransferType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('date')
            ->add('periodicity', ChoiceType::class,array('label' => 'Périodicité',
                                                         'choices'=> array('mensuelle'=>'1',
                                                                           'bimestrielle'=>'2',
                                                                           'trimestrielle'=>'3',
                                                                           'semestrielle' => '6',
                                                                           'annuelle'     => '12')))
           ->add('firstOccurrenceDate', DateType::class,array('label' => 'Première échéance')) 
           ->add('lastOccurrenceDate', DateType::class,array('label' => 'Dernière échéance')); 
    }

    public function getParent()
    {
        return TransferType::class;
    }
}

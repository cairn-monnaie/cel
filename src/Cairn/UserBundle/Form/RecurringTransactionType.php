<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class RecurringTransactionType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('periodicity', ChoiceType::class,array('label' => 'Périodicité',
                                                         'choices'=> array('mensuelle'=>'1',
                                                                           'bimestrielle'=>'2',
                                                                           'trimestrielle'=>'3',
                                                                           'semestrielle' => '6',
                                                                           'annuelle'     => '12')))
           ->add('firstOccurrenceDate', DateType::class,array('label' => 'Première échéance')) 
           ->add('lastOccurrenceDate', DateType::class,array('label' => 'Dernière échéance')); 

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\RecurringTransaction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_recurringtransaction';
    }

    public function getParent()
    {
        return TransactionType::class;
    }

}

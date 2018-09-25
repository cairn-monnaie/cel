<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\AccountType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TransferType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount',   NumberType::class, array('label'=>'Montant'))    
            ->add('date',     DateType::class          , array('label'=> 'Date d\'éxecution'))
            ->add('fromAccount', AccountType::class, array('label'=>'Compte à débiter',
                                                           'error_bubbling'=>true))
            ->add('toAccount',       AccountType::class, array('label'=>'Compte à créditer'))
            ->add('description'  , TextareaType::class       ,array('label' => 'motif', 'required' => false))
            ->add('save'   , SubmitType::class,         array('label' => 'Suivant'));
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'to_accounts' => null,
            'from_accounts' => null,
        ));
    }
}

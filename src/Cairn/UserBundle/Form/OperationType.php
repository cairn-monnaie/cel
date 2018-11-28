<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\AccountType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class OperationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('amount',   NumberType::class, array('label'=>'Montant',
                                                       'scale'=>2))    
            ->add('fromAccount', AccountType::class, array('label'=>'Compte à débiter',
                                                           'error_bubbling'=>true))
            ->add('toAccount',       AccountType::class, array('label'=>'Compte à créditer'))
            ->add('reason'  , TextareaType::class       ,array('label' => 'Motif', 'required' => false))
            ->add('description'  , TextType::class       ,array('label' => 'Description', 'required' => false))
            ->add('save'   , SubmitType::class,         array('label' => 'Suivant'));
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
        return 'cairn_userbundle_operation';
    }

}

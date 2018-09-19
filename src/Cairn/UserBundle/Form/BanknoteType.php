<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\BanknoteStatusType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BanknoteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('number', IntegerType::class,array('label'=>'N° d\'identification'))
            ->add('value' , IntegerType::class,array('label'=>'valeur'));
//            $builder->add('status', BanknoteStatusType::class,array('label'=>'statut'))
//            $builder->add('save'  , SubmitType::class,array('label'=>'Validation'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Banknote'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_banknote';
    }


}

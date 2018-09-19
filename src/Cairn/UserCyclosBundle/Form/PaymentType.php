<?php

namespace Cairn\UserCyclosBundle\Form;

use Cairn\UserCyclosBundle\Form\TransactionCategoryType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;

use Symfony\Component\OptionsResolver\OptionsResolver;


class PaymentType extends AbstractType
{
//    private $tokenstorage;
//
//    public function __construct(tokenstorageinterface $tokenstorage)
//    {
//        $this->tokenstorage = $tokenstorage;
//    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
//        $user = $this->tokenStorage->getToken()->getUser();

        $builder
            ->add('title'    ,TextType::class,array('label' => 'intitulé'))
            ->add('reason'  , TextareaType::class       ,array('label' => 'motif') )
            ->add('date',
            ->add('save'    , SubmitType::class,         array('label' => 'Suivant'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserCyclosBundle\Entity\Payment',
            'csrf_protection' => false
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_usercyclosbundle_payment';
    }


}

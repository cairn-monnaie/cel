<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Entity\Operation;
use Cairn\UserBundle\Entity\AccountScore;

class AccountScoreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('types', ChoiceType::class, array(
                        'choices' => Operation::getB2CTypes(),
                        'choice_label'=> function($choice){
                            return Operation::getTypeName($choice);
                        },
                        'multiple'=>true,
                        'expanded'=>false,
                        'label' => 'Types'
            ))
            ->add('format', ChoiceType::class, array(
                        'choices' => AccountScore::getPossibleTypes(),
                        'choice_label' => function ($choice, $key, $value) {
                            return $value;
                        },
                         'multiple'=>false,
                        'expanded'=>false,
                        'label' => 'Format'
            ))
            ->add('email' , TextType::class ,array('label' => 'Adresse email'))
            ->add('schedule', TextType::class, array(
                'attr'=> array('class'=>'timepicker'),
                'label' => 'heure de réception'
            )
        )
        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\AccountScore'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_accountscore';
    }


}

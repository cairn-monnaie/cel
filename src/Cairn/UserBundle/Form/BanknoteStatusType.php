<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserCyclosBundle\Repository\UserRepository;

class BanknoteStatusType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('status'        , ChoiceType::class,array('label'=>'statut',
                                                          'choices'=>array('désactiver'            => 'deactivated',
                                                                           'en circulation'        => 'circulating',
                                                                           'en comptoir de change' => 'stored'     )))
            ->add('exchangeOffice', EntityType::class,array('label'=>'Comptoir de change',
                                                            'class'=> 'CairnUserBundle:User',
                                                            'query_builder'=> function(UserRepository $ur){
                                                                $qb = $ur->createQueryBuilder('u');
                                                                $ur->whereRole($qb,'ROLE_ExOFF');
                                                                return $qb;
                                                            },
                                                            'choice_label'=>'username',
                                                        ));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\BanknoteStatus'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_banknotestatus';
    }


}

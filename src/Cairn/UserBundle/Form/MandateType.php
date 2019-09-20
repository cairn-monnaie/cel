<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;


class MandateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('contractor',  EntityType::class, array(
                'class'        => 'CairnUserBundle:User',
                'choice_label' => 'autocompleteLabel',
                'multiple'     => false,
            ))
            ->add('amount', NumberType::class, array('label'=>'Montant','scale'=>2,'attr'=>array()))
            ->add('beginAt', DateType::class, array('label'=> 'Début','widget' => 'single_text','format' => 'yyyy-MM-dd',"attr"=>array('class'=>'datepicker_cairn')))
            ->add('endAt', DateType::class, array('label'=> 'Fin','widget' => 'single_text','format' => 'yyyy-MM-dd',"attr"=>array('class'=>'datepicker_cairn')))
            ->add('mandateDocuments', CollectionType::class, array(
                'entry_type'   => MandateDocumentType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false
            ))
            ->add('forward', SubmitType::class, array('label' => 'Déclarer'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Mandate'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_mandate';
    }


}

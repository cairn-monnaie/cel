<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class PushTemplateType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('title', TextType::class,array('label'=>'Titre','required'=>false))
            ->add('content', TextareaType::class,array('label'=>'Contenu','required'=>false))
            ->add('actionTitle', TextType::class,array('label'=>'Action','required'=>false))
            ->add('redirectionUrl', UrlType::class,array('label'=>'Redirection web','required'=>false))
            ->add('save', SubmitType::class,array('label'=>'Envoyer maintenant'))
            ->add('cancel', SubmitType::class,array('label'=>'Annuler'));
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\PushTemplate'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_pushtemplate';
    }


}

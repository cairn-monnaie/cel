<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class ConfirmationType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('cancel',    SubmitType::class, array('label' => 'Annulation','attr' => array('class' => 'btn red')))
            ->add('save',      SubmitType::class, array('label' => 'Confirmation','attr' => array('class' => 'btn green')));
    }
}

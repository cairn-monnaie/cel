<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\BanknoteType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchBanknoteType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('start', DateTimeType::class,array('label' => 'Dernière modification après :','date_widget' =>     'single_text'))
            ->add('end',   DateTimeType::class,array('label' => 'Dernière modification avant :','date_widget' =>     'single_text'));
    }

    public function getParent()
    {
        return BanknoteType::class;
    }
}

<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\AbstractType;


use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class CardType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('field',   PasswordType::class, array('label'=>'Clé','attr'=>array('maxlength'=>4,'minlength'=>4,"pattern"=>"[0-9]+")))
            ->add('save',   SubmitType::class, array('label'=>'Associer'));

    }

}

<?php
// src/CairnUserBundle/Form/CurrentPasswordType.php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CurrentPasswordType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('current_password', PasswordType::class,array(
                'label'=>'Mot de passe actuel',
                'mapped'=>false,
                'constraints'=>new UserPassword() ));
    }


}

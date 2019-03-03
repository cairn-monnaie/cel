<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AccountType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email',   EmailType::class, array('label'=>'Email du bénéficiaire',
                                                    'required'=>false))    
            ->add('number',       TextType::class, array('label'=>'Identifiant Compte Cairn',
                                                     'required'=>false))
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $account = $event->getData();
                $form = $event->getForm();

                $account['number'] = preg_replace('/\s+/', '', $account['number']);
                $event->setData($account);
            });
    }

}

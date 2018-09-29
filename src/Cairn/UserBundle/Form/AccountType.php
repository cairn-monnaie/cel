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
            ->add('id',       TextType::class, array('label'=>'ICC',
                                                     'required'=>false))
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $account = $event->getData();
                $form = $event->getForm();

                $account['id'] = preg_replace('/\s/', '', $account['id']);
                $event->setData($account);
            });
    }

}

<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\DataTransformer\AccountToStringTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class AccountType extends AbstractType
{

    private $transformer;

    public function __construct(AccountToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function getBlockPrefix()
    {
        return 'account';
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected data does not match any active user account',
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
        $builder
            ->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
                $account = $event->getData();

                $account = trim($account);
                $event->setData($account);
            });
    }

}

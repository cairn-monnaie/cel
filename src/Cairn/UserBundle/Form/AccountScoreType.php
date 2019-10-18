<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Entity\AccountScore;

use Cairn\UserBundle\Form\AccountScoreScheduleType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AccountScoreType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        
        $builder
            ->add('format', ChoiceType::class, array(
            'choices' => AccountScore::getPossibleTypes(),
            'choice_label' => function ($choice, $key, $value) {
                return $value;
            },
             'multiple'=>false,
            'expanded'=>false,
            'label' => 'Format'
        ));

        $builder->addEventListener( // change options depending on if mandate is created or edited
            FormEvents::POST_SET_DATA,
            function (FormEvent $event){
                $accountScore = $event->getData();
                $form = $event->getForm();
                if(null === $accountScore){
                    return;
                }

                $defaultEmail = ($email = $accountScore->getEmail()) ? $email : $accountScore->getUser()->getEmail();
                $form->add('email' , TextType::class ,array('label' => 'Adresse email','data'=>$defaultEmail));
                $form->add('schedule' , AccountScoreScheduleType::class ,array(
                        'label' => 'Programme',
                        'data'=>$accountScore->getSchedule(),
                        'error_bubbling' => false
                    ));

            }
        );

        $builder->add('save'   , SubmitType::class,         array('label' => 'Enregistrer','attr'=>array('class'=>'right')));

        ;
    }/**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\AccountScore'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_accountscore';
    }


}

<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class AccountScoreScheduleType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $days = [
            'Sun',
            'Mon',
            'Tue',
            'Wed',
            'Thu',
            'Fri',
            'Sat'
        ];

        $builder->addEventListener( // change options depending on if mandate is created or edited
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) use ($days) {
                $schedule = $event->getData();
                $form = $event->getForm();
                if(null === $schedule){
                    return;
                }

                foreach($days as $day){
                    $form->add($day, CollectionType::class, array(
                        'data'=> $schedule[$day],
                        'entry_type'   => TextType::class,
                        'entry_options' => array(
                            'attr'=> array('class'=>'timepicker time-input'),
                        ),
                        'prototype' => true,
                        'allow_add'    => true,
                        'allow_delete' => true,
                        'by_reference' => false,
                        'label' => $day
                    ));

                }
                
            }
        );
    }

}

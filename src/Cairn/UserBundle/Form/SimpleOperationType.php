<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Cairn\UserBundle\Entity\Operation;

class SimpleOperationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) {
                $operation = $event->getData();
                $form = $event->getForm();

                $transactionTypes = array(Operation::TYPE_TRANSACTION_EXECUTED,Operation::TYPE_TRANSACTION_SCHEDULED);
                if(in_array($operation->getType(),$transactionTypes)){ 
                    $form->add('executionDate',     DateType::class          , array('label'=> 'Date d\'exécution','widget' => 'single_text','format' => 'dd-MM-yyyy',"attr"=>array('class'=>'datepicker_cairn')));
                }
                if(in_array($operation->getType(),Operation::getToOperationTypes())){
                    $form->remove('fromAccount');
                }
                if(in_array($operation->getType(),Operation::getDebitOperationTypes())){ 
                    $form->remove('toAccount');
                }
            });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Operation'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_simpleoperation';
    }

    public function getParent()
    {
        return OperationType::class;
    }

}



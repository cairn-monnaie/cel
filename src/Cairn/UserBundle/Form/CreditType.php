<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\TransferType;

use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class CreditType extends TransferType
{
    private $authorizationChecker;

    public function __construct(AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->authorizationChecker = $authorizationChecker;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $isAdmin = $this->authorizationChecker->isGranted('ROLE_ADMIN');
        if (!$isAdmin) {
            throw new AccessDeniedException(
                'Vous n\'avez pas les droits nécessaires.'
            );
        }

        $fromAccounts = $options['from_accounts'];
        $toAccounts =  $options['to_accounts'];

        $builder->remove('date');
         $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($currentUser,$fromAccounts,$toAccounts) {
                         $form = $event->getForm();
                         if($isAdmin){
                              $form->add('toAccount', AccountType::class, array('label'=>'Compte à  créditer'));  
                         }else{
                             $form->get('fromAccount')->setData('property',$fromAccounts[0]);
//                             $form->add('fromAccount', ChoiceType::class, array('label'=>'Compte à débiter',
//                                                                                'choices'=> $fromAccounts,
//                                                                                'choice_label' =>'id',
//                                                                                'data'=>$fromAccounts[0],
//                                                                                'disabled'=>true))
                              $form->add('toAccount',       ChoiceType::class, array('label'=>'Compte à créditer',
                                'choice_label'=>'id',
                                'choices' => $toAccounts));
                         }
         });

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'to_accounts' => null,
            'from_accounts' => null,
        ));
    }

    public function getParent()
    {
         return TransferType::class;
    }       

}

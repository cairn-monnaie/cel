<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\AccountType;
use Cairn\UserBundle\Service\BridgeToSymfony;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class ReconversionType extends AbstractType
{

     private $tokenStorage;
    /**
     * @var AccountInfo
     */
    private $accountInfoService;


    public function __construct(TokenStorageInterface $tokenStorage, AccountInfo $accountInfo)
    {
        $this->tokenStorage = $tokenStorage;
        $this->accountInfoService = $accountInfo;
    }
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        // grab the user, do a quick sanity check that one exists
        $user = $this->tokenStorage->getToken()->getUser();
        if (!$user) {
            throw new \LogicException(
                'cannot be used without an authenticated user!'
            );
        }

        $builder
            ->add('amount',   NumberType::class, array('label'=>'Montant','scale'=>2,'attr'=>array()));

        $builder->add('reason'  , TextType::class       ,array('label' => 'Motif court'))
            ->add('description'  , TextareaType::class       ,array('label' => 'Motif long', 'required' => false))
            ->add('save'   , SubmitType::class,         array('label' => 'Effectuer','attr'=>array('class'=>'right')));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            $form = $event->getForm();
            $operation = $event->getData();
            if (is_object($user)) {
                $selfAccounts = $this->accountInfoService->getAccountsSummary($user->getCyclosID());

                $form->add('fromAccount', ChoiceType::class, array(
                    'placeholder' => (count($selfAccounts) > 1) ? '--- virement depuis ---' : false,
                    'choices' => $selfAccounts,
                    'choice_label' =>
                        function($account, $key, $index) {
                            return $account->type->name.' ['.$account->status->balance.' '.$account->currency->name.']';
                        },
                    'choice_value' =>
                        function($account) {
                            return ($account != null) ? $account->number : '';
                        },
                    'multiple' => false,
                    'required' => true,
                    'label' => 'Compte à débiter'
                ));
                

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
        return 'cairn_userbundle_reconversion';
    }

}


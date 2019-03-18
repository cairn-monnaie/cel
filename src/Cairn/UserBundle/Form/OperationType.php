<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\AccountType;
use Cairn\UserBundle\Service\BridgeToSymfony;
use Cairn\UserCyclosBundle\Service\AccountInfo;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
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


class OperationType extends AbstractType
{

    private $tokenStorage;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var AccountInfo
     */
    private $accountInfoService;
    /**
     * @var BridgeToSymfony
     */
    private $bridgeToSymfonyService;


    public function __construct(TokenStorageInterface $tokenStorage, ValidatorInterface $validator, AccountInfo $accountInfo,BridgeToSymfony $bridgeToSymfony)
    {
        $this->tokenStorage = $tokenStorage;
        $this->validator = $validator;
        $this->accountInfoService = $accountInfo;
        $this->bridgeToSymfonyService = $bridgeToSymfony;
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
                $debitorVO = $this->bridgeToSymfonyService->fromSymfonyToCyclosUser($user);
                $selfAccounts = $this->accountInfoService->getAccountsSummary($debitorVO->id);

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


                //No matter the type of creditor, the final object must contain two attributes : email && number
                if ($operation->getToAccountNumber()=='beneficiary'){
                    $beneficiaries = $user->getBeneficiaries();

                    foreach($beneficiaries as $beneficiary){                           
                        $account = new \stdClass();
                        $account->owner = $beneficiary->getUser();
                        $account->email = $beneficiary->getUser()->getEmail();
                        $account->number = $beneficiary->getICC();
                        $toAccounts[] = $account;// array('email'=>$beneficiary->getUser()->getEmail(),'number'=>$beneficiary->getICC());
                    }
                    $form->add('toAccount', ChoiceType::class, array(
                        'placeholder' => (count($toAccounts) > 1) ? '--- virement à ---' : false,
                        'choices' => $toAccounts,
                        'choice_label' =>
                            function($account, $key, $index) {
                                return $account->owner->getAutocompleteLabel()."\n".$account->number;
                        },
                        'choice_value' => 'number',
                        'multiple' => false,
                        'required' => true,
                        'label' => 'Beneficiaire à créditer'
                    ));
                }elseif($operation->getToAccountNumber()=='self'){
                    $form->add('toAccount', ChoiceType::class, array(
                        'placeholder' => (count($selfAccounts) > 1) ? '--- virement vers mon compte ---' : false,
                        'choices' => $selfAccounts,
                        'choice_label' => 'type.name',
                        'choice_value' => 'number',
                        'multiple' => false,
                        'required' => true,
                        'label' => 'Mon compte à créditer'
                    ));
                }else{
                    $form->add('toAccount',  AccountType::class, array('label'=>'Compte à créditer'));
                }
                $operation->setToAccountNumber(null);
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
        return 'cairn_userbundle_operation';
    }

}

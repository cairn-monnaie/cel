<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\AccountType;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Validator\ValidatorInterface;


class TransactionType extends AbstractType
{
    private $tokenStorage;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var EntityManager
     */
    private $em;


    public function __construct(TokenStorageInterface $tokenStorage, ValidatorInterface $validator)
    {
        $this->tokenStorage = $tokenStorage;
        $this->validator = $validator;
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
            ->add('amount',     NumberType::class, array('label'=>'Montant', 'scale'=>2))
            ->add('toAccount',  AccountType::class, array('label'=>'Compte à créditer'))
            ->add('description',TextareaType::class, array('label' => 'motif', 'required' => false))
            ->add('save',       SubmitType::class, array('label' => 'Suivant'));

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($user) {
            $form = $event->getForm();
            if (is_object($user)) {
                $accountService = $this->get('cairn_user_cyclos_account_info');
                $symfonyCyclosBidge = $this->get('cairn_user.bridge_symfony');
                $debitorVO = $symfonyCyclosBidge->fromSymfonyToCyclosUser($user);
                $selfAccounts = $accountService->getAccountsSummary($debitorVO->id);

                $form->add('fromAccount', EntityType::class, array(
                    'class' => AccountType::class,
                    'placeholder' => '--- from ---',
                    'choices' => $selfAccounts,
                    'choice_label' => 'name',
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
            'data_class' => 'Cairn\UserBundle\Entity\Transaction'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_transaction';
    }

}

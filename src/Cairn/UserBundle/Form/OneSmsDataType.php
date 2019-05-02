<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Cairn\UserBundle\Validator\UserPhoneNumber;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\HttpFoundation\RequestStack;

class OneSmsDataType extends AbstractType
{

    private $authorizationChecker;

    protected $requestStack;                                                   

    public function __construct(AuthorizationChecker $authorizationChecker,RequestStack $requestStack)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;     
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $session = $this->requestStack->getCurrentRequest()->getSession();
        if($session->has('activationCode')){
            $builder->add('activationCode',TextType::class, array('label'=>'Code activation','mapped'=>false))
                ->add('save', SubmitType::class, array('label' => 'Valider'));

        }else{
            $builder
                ->add('phoneNumber',   TextType::class, array('label'=>'Numéro de téléphone portable',
                                                              'constraints'=>new UserPhoneNumber() ))
                ->add('smsEnabled',    CheckboxType::class, array('label'=>'Autoriser les opérations SMS',
                    'required'=>false));
            //           ->add('dailyAmountThreshold', IntegerType::class, array('label'=>'Montant max/jour en SMS sans validation',
            //                       'constraints'=> new Assert\Range(array('min'=> 0,'max'=>50,
            //                                                 'minMessage' => 'Un nombre négatif n\'a pas de sens !',
            //                                                 'maxMessage' => 'Au dessus de 50, la carte de sécurité est obligatoire'))
            //           ))
            //           ->add('dailyNumberPaymentsThreshold'  , IntegerType::class ,array('label' => 'Nombre de paiements SMS / jour sans validation',
            //                       'constraints'=> new Assert\Range(array('min'=> 0,'max'=>5,
            //                                                 'minMessage' => 'Un nombre négatif n\'a pas de sens !',
            //                                                 'maxMessage' => 'Au dessus de 5 paiements dans la même journée, la carte de sécurité est obligatoire'))
            //           ))

            $builder->addEventListener(
                FormEvents::POST_SET_DATA,
                function (FormEvent $event) {
                    $smsData = $event->getData();
                    $form = $event->getForm();


                    if(null === $smsData){
                        return;
                    }

                    if($smsData->getUser()->hasRole('ROLE_PRO')){
                        $form->add('paymentEnabled',CheckboxType::class, array('label'=>'Autoriser la réalisation de paiements par SMS depuis ce numéro','required'=>false))
                            ->add('smsEnabled',  CheckboxType::class, array('label'=>'Autoriser la réception de paiements par SMS',
                                'required'=>false));

                        if($this->authorizationChecker->isGranted('ROLE_ADMIN')){
                            $form->add('identifier', TextType::class, array('label' => 'ID SMS'))
                                ->remove('phoneNumber');
                        }

                    }


                });

            $builder->add('save', SubmitType::class, array('label' => 'Suivant'));
        }

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\SmsData'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_onesmsdata';
    }

}

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

use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Repository\SmsDataRepository;

class OneSmsDataType extends AbstractType
{

    private $authorizationChecker;

    protected $requestStack;                                                   

    private $smsDataRepo;

    public function __construct(AuthorizationChecker $authorizationChecker,RequestStack $requestStack,SmsDataRepository $smsDataRepo)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->smsDataRepo = $smsDataRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $request = $this->requestStack->getCurrentRequest();
        $session= $request->getSession();
        if($session->has('activationCode')){
            $builder->add('activationCode',TextType::class, array('label'=>'Code activation','mapped'=>false))
                ->add('save', SubmitType::class, array('label' => 'Valider'));

        }else{
            $builder
                ->add('phoneNumber',   TextType::class, array('label'=>'Numéro de téléphone portable(format +33)',
                                                              'constraints'=>new UserPhoneNumber($request) ))
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

                    $ownerUser = $smsData->getUser();
                    if($ownerUser->hasRole('ROLE_PRO')){
                        if(! $smsData->getIdentifier()){

                            $identifier = SmsData::makeIdentifier($ownerUser->getName());

                            $sb = $this->smsDataRepo->createQueryBuilder('s');
                            $smsDataWithIdentifier = $sb->andWhere('s.user = :user')
                                ->orderBy('s.identifier','DESC')
                                ->setParameter('user',$ownerUser)
                                ->getQuery()->getResult();

                            if( count($smsDataWithIdentifier)){
                                //@TODO : apply this process for names with numbers
                                //retrieve the first one, because we ordered DESC
                                $count = 1;
                                $firstIdentifier = $smsDataWithIdentifier[0]->getIdentifier();
                                if(preg_match_all('/\d+$/', $firstIdentifier, $numbers)) {
                                    $count = end($numbers[0]) + 1;
                                    $identifier = preg_replace('/'.end($numbers[0]).'$/',$count, $firstIdentifier);
                                }else{
                                    $identifier = $firstIdentifier . $count;
                                }
                            }else{
                                while($this->smsDataRepo->findOneByIdentifier($identifier)){
                                    $extra = strtoupper(chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)));
                                    $identifier = SmsData::makeIdentifier($ownerUser->getName(),$extra);
                                }
                            }

                            $smsData->setIdentifier($identifier);
                        }

                        $form->add('paymentEnabled',CheckboxType::class, array('label'=>'Autoriser la réalisation de paiements par SMS depuis ce numéro','required'=>false))
                            ->add('smsEnabled',  CheckboxType::class, array('label'=>'Autoriser la réception de paiements par SMS',
                                'required'=>false))
                            ->add('identifier', TextType::class, array('label' => 'ID SMS','disabled'=>true));

                        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
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

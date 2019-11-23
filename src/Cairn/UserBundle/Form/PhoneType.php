<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\NotificationPermissionType;

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
use Cairn\UserBundle\Entity\Phone;
use Cairn\UserBundle\Repository\PhoneRepository;

class PhoneType extends AbstractType
{

    private $authorizationChecker;

    protected $requestStack;                                                   

    private $phoneRepo;

    public function __construct(AuthorizationChecker $authorizationChecker,RequestStack $requestStack,PhoneRepository $phoneRepo)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->requestStack = $requestStack;
        $this->phoneRepo = $phoneRepo;
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
                ->add('phoneNumber',   TextType::class, array('label'=>'Numéro de téléphone portable(format +336 ou +337)',
                                                              'constraints'=>new UserPhoneNumber($request) ))
                ->add('paymentEnabled',    CheckboxType::class, array('label'=>'Autoriser les opérations SMS',
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
                    $phone = $event->getData();
                    $form = $event->getForm();


                    if(null === $phone){
                        return;
                    }

                    $ownerUser = $phone->getUser();
                    if($ownerUser->hasRole('ROLE_PRO')){
                        if(! $phone->getIdentifier()){

                            $identifier = Phone::makeIdentifier($ownerUser->getName());

                            $pb = $this->phoneRepo->createQueryBuilder('p');
                            $phonesWithIdentifier = $pb->join('p.smsData','s')
                                ->andWhere('s.user = :user')
                                ->setParameter('user',$ownerUser)
                                ->orderBy('p.identifier','DESC')
                                ->getQuery()->getResult();

                            if( count($phonesWithIdentifier)){
                                //@TODO : apply this process for names with numbers
                                //retrieve the first one, because we ordered DESC
                                $count = 1;
                                $firstIdentifier = $phonesWithIdentifier[0]->getIdentifier();
                                if(preg_match_all('/\d+$/', $firstIdentifier, $numbers)) {
                                    $count = end($numbers[0]) + 1;
                                    $identifier = preg_replace('/'.end($numbers[0]).'$/',$count, $firstIdentifier);
                                }else{
                                    $identifier = $firstIdentifier . $count;
                                }
                            }else{
                                while($this->phoneRepo->findOneByIdentifier($identifier)){
                                    $extra = strtoupper(chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)));
                                    $identifier = SmsData::makeIdentifier($ownerUser->getName(),$extra);
                                }
                            }

                            $phone->setIdentifier($identifier);
                        }

                        //list of possible payment notifications
                        $form->add('identifier', TextType::class, array('label' => 'ID SMS','disabled'=>true));

                        if($this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
                            $form->add('identifier', TextType::class, array('label' => 'ID SMS'))
                                ->remove('phoneNumber');
                        }

                    }

                });
        }
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Phone'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_phone';
    }

}

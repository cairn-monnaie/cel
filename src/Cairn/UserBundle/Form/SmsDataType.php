<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

use Cairn\UserBundle\Entity\SmsData;
use Cairn\UserBundle\Repository\SmsDataRepository;

class SmsDataType extends AbstractType
{

    private $authorizationChecker;

    private $smsDataRepo;

    public function __construct(AuthorizationChecker $authorizationChecker, SmsDataRepository $smsDataRepo)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->smsDataRepo = $smsDataRepo;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('phoneNumber', TextType::class, array('label'=>'Numéro de téléphone portable'))
            ->add('smsEnabled',  CheckboxType::class, array('label'=>'Autoriser les opérations par SMS',
                                                              'value'=>true,
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

                if($smsData->getUser()->hasRole('ROLE_PRO') ){

                    if(! $smsData->getIdentifier()){
                        $identifier = SmsData::makeIdentifier($smsData->getUser()->getName());

                        $sb = $this->smsDataRepo->createQueryBuilder('s');
                        $smsDataIdentifiers = $sb->where($sb->expr()->like('s.identifier', $sb->expr()->literal($identifier . '%')))
                            ->getQuery()->getResult();

                        if( count($smsDataIdentifiers)){
                            $extra = strtoupper(chr(rand(65,90)) . chr(rand(65,90)) . chr(rand(65,90)));
                            $identifier = SmsData::makeIdentifier($smsData->getUser()->getName(),$extra);
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
        return 'cairn_userbundle_smsdata';
    }

}

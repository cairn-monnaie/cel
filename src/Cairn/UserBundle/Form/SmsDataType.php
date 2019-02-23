<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\AbstractType;

use Cairn\UserBundle\Validator\UserPhoneNumber;

use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class SmsDataType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
            ->add('phoneNumber',   TextType::class, array('label'=>'Numéro de téléphone portable',
                                                          'constraints'=> new UserPhoneNumber()
            ))
            ->add('smsEnabled', CheckboxType::class, array('label'=>'Autoriser les  actions SMS',
                                                          'required'=>false))
            ->add('dailyAmountThreshold', IntegerType::class, array('label'=>'Montant max/jour en SMS sans validation',
                        'constraints'=> new Assert\Range(array('min'=> 0,'max'=>50,
                                                  'minMessage' => 'Un nombre négatif n\'a pas de sens !',
                                                  'maxMessage' => 'Au dessus de 50, la carte de sécurité est obligatoire'))
            ))
            ->add('dailyNumberPaymentsThreshold'  , IntegerType::class ,array('label' => 'Nombre de paiements SMS / jour sans validation',
                        'constraints'=> new Assert\Range(array('min'=> 0,'max'=>5,
                                                  'minMessage' => 'Un nombre négatif n\'a pas de sens !',
                                                  'maxMessage' => 'Au dessus de 5 paiements dans la même journée, la carte de sécurité est obligatoire'))
            ))
            ->add('save', SubmitType::class, array('label' => 'Suivant'));
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

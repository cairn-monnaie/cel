<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('street1', TextType::class,array('label'=> 'Rue',
                'constraints'=> new Assert\Length(array('min'=> 5,'max'=>100,
                                                  'minMessage' => 'too_short',
                                                  'maxMessage' => 'too_long'))
                                              ))
            ->add('street2', TextType::class,array('required'=>false,'label'=> 'Complément d\'adresse',
                  'constraints'=> new Assert\Length(array('min'=> 5,'max'=>100,
                                                  'minMessage' => 'too_short',
                                                  'maxMessage' => 'too_long'))
                                              ))
            ->add('zipCity', ZipCitySelectorType::class, array(
                                                     'label' => 'Code postal & ville',
                                                     'required' => true,
            ));
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\Address'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_address';
    }


}

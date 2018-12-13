<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Form\ZipCityType;
use Cairn\UserBundle\Entity\ZipCity;

use Symfony\Component\Form\AbstractType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Doctrine\ORM\EntityRepository;

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
            ->add('street1', TextType::class,array('label'=> 'Adresse 1',
                'constraints'=> new Assert\Length(array('min'=> 5,'max'=>40,
                                                  'minMessage' => 'Adresse trop courte',
                                                  'maxMessage' => 'Adresse trop longue'))
                                              ))
            ->add('street2', TextType::class,array('required'=>false,'label'=> 'Adresse 2',
                  'constraints'=> new Assert\Length(array('min'=> 5,'max'=>40,
                                                  'minMessage' => 'Adresse trop courte',
                                                  'maxMessage' => 'Adresse trop longue'))
                                              ))
            ->add('zipCity', EntityType::class, array(
                                                     'class'=>ZipCity::class,
                                                     'query_builder' => function (EntityRepository $er) {
                                                                 return $er->createQueryBuilder('z')
                                                                                 ->orderBy('z.zipCode', 'ASC');
                                                                     },
                                                     'choice_label'=>'zipCode',
                                                     'choice_value'=>'zipCode'
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

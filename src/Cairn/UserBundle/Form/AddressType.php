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

class AddressType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('street', TextType::class)
            ->add('zipCity', EntityType::class, array(
                                                     'class'=>ZipCity::class,
                                                     'query_builder' => function (EntityRepository $er) {
                                                                 return $er->createQueryBuilder('z')
                                                                                 ->orderBy('z.zipCode', 'ASC');
                                                                     },
                                                     'choice_label'=>'zipCode'
            ));
    }/**
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

<?php
/**
 * Created by PhpStorm.
 * User: gjanssens
 * Date: 03/03/19
 * Time: 09:59
 */

namespace Cairn\UserBundle\Form;


use Cairn\UserBundle\Form\DataTransformer\ZipCityToStringTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ZipCitySelectorType extends AbstractType
{
    private $transformer;

    public function __construct(ZipCityToStringTransformer $transformer)
    {
        $this->transformer = $transformer;
    }

    public function getBlockPrefix()
    {
        return 'zipcities';
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'invalid_message' => 'The selected zipcity does not exist',
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }

}
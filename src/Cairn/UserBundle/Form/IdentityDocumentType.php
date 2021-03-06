<?php

namespace Cairn\UserBundle\Form;

use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class IdentityDocumentType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('file', FileType::class,array('label'=>'identityDocument',
                'constraints'=>array(
                    new Assert\File(array(
                        'maxSize'=>'5M',
                        'maxSizeMessage'=>'Fichier trop volumineux ({{ size }} {{ suffix }}). La taille maximale est {{ limit }} {{ suffix }}',
                        'mimeTypesMessage'=>'Les formats valides sont pdf, jpeg, jpg, et png',
                        'mimeTypes'=>array('image/jpeg','image/jpg','image/png','application/pdf')

                    )),
                    //new Assert\NotNull(array(
                    //    'message'=>'Ce document est obligatoire'
                    //)),

                ),
            ));

    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\File'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_userbundle_id_document';
    }


//    public function getParent()
//    {
//        return FileType::class;
//    }

}

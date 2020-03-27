<?php

namespace Cairn\UserBundle\Form;

use Cairn\UserBundle\Validator\UserPassword;
use Cairn\UserBundle\Service\Api;

use FOS\UserBundle\Form\Type\ProfileFormType;
use Cairn\UserBundle\Form\AddressType;
use Cairn\UserBundle\Form\IdentityDocumentType;
use Cairn\UserBundle\Form\ImageType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Validator\Constraints as Assert;

use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

class ProfileType extends AbstractType
{
    private $authorizationChecker;

    private $apiService;

    public function __construct(AuthorizationChecker $authorizationChecker, Api $apiService)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->apiService = $apiService;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //The only data that do not depend on the user role are current user's passsword and address
        $builder
            //->add('current_password', PasswordType::class,array(
            //    'label'=>'Mot de passe actuel',
            //    'mapped'=>false,
            //    'constraints'=>new UserPassword() 
            //))
            ->add('address' , AddressType::class)
            ->remove('current_password');

        $builder->add('username',TextType::class,array('label'=>'Nom d\'utilisateur'));
        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $user = $event->getData();
                $form = $event->getForm();
                if(null === $user){
                    return;
                }
                //if($this->apiService->isRemoteCall()){
                //    $form->remove('current_password');
                //}
                if($user->hasRole('ROLE_PRO')){
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure'))
                        ->add('description',TextareaType::class,array('label'=>'Description d\'activité en quelques mots (150 car.)'))
                        ->add('image', ImageType::class,array('label'=>'Logo'));

                }elseif($user->hasRole('ROLE_PERSON')){
                    $form->add('name', TextType::class,array('label'=>'Nom et prénom'));
                    $form->add('description',TextareaType::class,array('label'=>
                        'Décrivez ici en quelques mots pourquoi vous utilisez le Cairn :) '));
                }else{
                    $form->add('name', TextType::class,array('label'=>'Nom de la structure admin'));
                    $form->add('description',TextareaType::class,array('label'=>
                        'Décrivez ici en quelques mots son rôle au sein du Cairn :) '));
                }

                if(! $this->authorizationChecker->isGranted('ROLE_SUPER_ADMIN')){
                    $disabledFields = array('name','username');

                    foreach($disabledFields as $fieldName){
                        $myField = $form->get($fieldName)->getConfig();
                        $fieldOptions = $myField->getOptions();
                        // Retrieve the FormType. That is the part that is different.
                        $fieldType = get_class($myField->getType()->getInnerType());
                        $fieldOptions['disabled'] = true;
                        // I can obviously put the name 'my_field' directly here
                        $form->add($myField->getName(), $fieldType, $fieldOptions);
                    }
                }else{
                    $label = ($user->hasRole('ROLE_PRO')) ? 'Justificatif d\'activité professionnelle' :'Pièce d\'identité';

                    $form->add('identityDocument', IdentityDocumentType::class,
                        array(
                            'label'=>$label,
                            'attr' => array('class'=>'identity-document'),
                            'required' => false
                        ))
                        ->add('initialize_parameters', CheckboxType::class, array('label'=>'Réinitialiser les paramètres',
                            'mapped'=>false,
                            'required'=>false));

                }

            }
        );
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Cairn\UserBundle\Entity\User'
        ));
    }

    public function getParent()
    {
        return ProfileFormType::class;
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'cairn_user_profile_edit';
    }


}

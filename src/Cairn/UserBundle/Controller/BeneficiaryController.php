<?php

namespace Cairn\UserBundle\Controller;

use Cairn\UserBundle\Form\AccountType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Cyclos
use Cyclos;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Beneficiary;
use Cairn\UserBundle\Entity\Card;
use Cairn\UserBundle\Entity\Operation;
use Cairn\UserCyclosBundle\Entity\UserManager;
use Cairn\UserCyclosBundle\Entity\ScriptManager;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
//manage Forms
use Cairn\UserBundle\Form\ConfirmationType;
use Cairn\UserBundle\Form\RegistrationType;
use Cairn\UserBundle\Form\CardType;
use Cairn\UserBundle\Form\BeneficiaryType;
use Cairn\UserBundle\Form\ProfileFormType;
use Cairn\UserBundle\Form\ChangePasswordType;

use Cairn\UserBundle\Validator\UserPassword;

use Symfony\Component\Form\AbstractType;                                       
use Symfony\Component\Form\FormBuilderInterface;                               
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;                   
use Symfony\Component\Form\Extension\Core\Type\PasswordType;                   
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Symfony\Component\Validator\Constraints as Assert;

/**
 * This class is a CRUD related to user's beneficiaries
 *
 * @Security("is_granted('ROLE_ADHERENT')")
 */
class BeneficiaryController extends Controller
{
    private $userManager;
    //    private $scriptManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    /* List API options related to beneficiary URI 
     *
     */
    public function optionsAction(Request $request)                           
    {   
        $template = array(
            'notes'=> '',
            'path'=> '',
            'method'=> '',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(),
                'access denied'=> array(),
                'error'=> array()
            )
        );

        $options = array();
        $options['get beneficiaries list'] = array(
            'notes'=> 'Request list of beneficiaries',
            'path'=> 'api/beneficiaries/get',
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['get specific beneficiary'] = array(
            'notes'=> 'Request beneficiary data with id {id}',
            'path'=> "api/beneficiaries/get/{id}",
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'beneficiary id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful request'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'forbidden access'
                 ),
                'undefined beneficiary'=> array(
                     'status code'=> '404',
                     'reason'=> 'no beneficiary with given id'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['post'] = array(
            'notes'=> 'Create a new beneficiary for current user',
            'path'=> 'api/beneficiaries/post',
            'method'=> 'POST',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name',
                        'data_type'=> 'string'
                    ),
                    'email'=>array(
                        'description'=> 'User email',
                        'data_type'=> 'string'
                    ),
                    'ICC'=>array(
                        'description'=> 'Target user\'s account number(required)',
                        'data_type'=> 'int'
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '201',
                     'reason'=> 'successful beneficiary addition'
                ),
                'bad request'=> array(
                     'status code'=> '400',
                     'reason'=> array(
                        'no user matched with provided name / email',
                        'wrong trial : cannot add yourself as beneficiary',
                        'wrong trial : already beneficiary',
                        'provided ICC does not match one of user\'s accounts'
                     )
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );        

        $options['delete'] = array(
            'notes'=> 'Request delete beneficiary with id {id}',
            'path'=> 'api/beneficiaries/delete/{id}',
            'method'=> 'POST',
            'Parameters'=> array(
                'query'=>array(),
                'body'=>array(
                     'save'=>array(
                        'description'=> 'Confirm deletion or not',
                        'data_type'=> 'boolean'
                    ),
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful beneficiary deletion'
                ),
                'bad request'=> array(
                    'status code'=> '400',
                    'reason'=> 'forbidden deletion on given beneficiary => not one of your beneficiaries'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        return new Response(json_encode($options));
    }

    /**
     * Get the list of beneficiaries for current User
     *
     */
    public function listBeneficiariesAction(Request $request, $_format)
    {
        $beneficiaries = $this->getUser()->getBeneficiaries();

        $form = $this->createForm(ConfirmationType::class);

        if($_format == 'json'){
            $beneficiaries = $this->get('cairn_user.api')->serialize($beneficiaries->getValues());

            $response = new Response($beneficiaries);
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        return $this->render('CairnUserBundle:Pro:list_beneficiaries.html.twig',array('form'=>$form->createView(),'beneficiaries'=>$beneficiaries));
    }


    /**
     * Adds a new beneficiary to the existing list
     *
     * This action is considered as a sensible operation
     * Proposes a list of potential users with autocompletion, then checks if the user and the ICC match, before ensuring that the
     * beneficiary is valid and adding it.
     *
     * As the User and Beneficiary class have a ManyToMany bidirectional relationship, adding it the two directions must be done
     *
     */
    public function addBeneficiaryAction(Request $request,$_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');

        $currentUser = $this->getUser();

        $possiblePros = $userRepo->myFindByRole(array('ROLE_PRO'));
        $possiblePersons = $userRepo->myFindByRole(array('ROLE_PERSON'));
        $possibleBeneficiaries = array_merge($possiblePros, $possiblePersons);

        $form = $this->createFormBuilder()
            ->add('cairn_user', AccountType::class, array('label' => 'Nom du bénéficiaire','attr'=>array('placeholder'=>'email, nom ou numéro de compte')))
            ->add('add', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();

        if($request->isMethod('POST')){

            if($_format == 'json'){
                $form->submit(json_decode($request->getContent(), true));
            }else{
                $form->handleRequest($request);
            }

            $apiService = $this->get('cairn_user.api');

            if($form->isValid()){
                $dataForm = $form->getData();

                $errorMessages = NULL;

                if ($dataForm['cairn_user']->id == $currentUser->getCyclosID()){
                    $errorMessages = array();
                    $errorMessages[] = 'Oups, vous ne pouvez pas vous ajouter vous-même...';
                }

                $creditorUser = $this->get('cairn_user.bridge_symfony')->fromCyclosToSymfonyUser($dataForm['cairn_user']->id);

                //check that beneficiary is not already in database, o.w create new one
                $existingBeneficiary = $beneficiaryRepo->findOneBy(array('user'=>$creditorUser,'ICC'=>$dataForm['cairn_user']->accountNumber));

                if($existingBeneficiary && $currentUser->hasBeneficiary($existingBeneficiary)){
                    $errorMessages[] = $creditorUser->getName().' est déjà un bénéficiaire enregistré ';
                }

                if($errorMessages){
                    if( $this->get('cairn_user.api')->isRemoteCall()){
                        return $this->get('cairn_user.api')->getErrorResponse($errorMessages ,Response::HTTP_BAD_REQUEST);
                    }else{
                        foreach($errorMessages as $message){
                            $session->getFlashBag()->add('error',$message);
                        }
                        return new RedirectResponse($request->getRequestUri());
                    }
                }

                if(! $existingBeneficiary){
                    $beneficiary = new Beneficiary();
                    $beneficiary->setUser($creditorUser);
                    $beneficiary->setICC($dataForm['cairn_user']->accountNumber);
                }
                else{ 
                    $beneficiary = $existingBeneficiary;
                }

                $beneficiary->addSource($currentUser);
                $currentUser->addBeneficiary($beneficiary);
                $em->persist($beneficiary);
                $em->persist($currentUser);
                $em->flush();


                if( $apiService->isRemoteCall()){
                    $res = $this->get('cairn_user.api')->serialize($beneficiary);
                    $response = new Response($res);
                    $response->setStatusCode(Response::HTTP_CREATED);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $session->getFlashBag()->add('success','Nouveau bénéficiaire ajouté avec succès');
                return $this->redirectToRoute('cairn_user_beneficiaries_list');
            }else{

                if( $apiService->isRemoteCall()){

                    return $apiService->getFormErrorResponse($form);
//                    $errors = [];
//                    foreach ($form->getErrors(true) as $error) {
//                        $errors[] = $error->getMessage();
//                    }
//                    $response = new Response(json_encode($errors));
//                    $response->setStatusCode(Response::HTTP_BAD_REQUEST);
//                    $response->headers->set('Content-Type', 'application/json');
//                    return $response;
                }
            }
        }

        return $this->render('CairnUserBundle:Pro:add_beneficiaries.html.twig',array('form'=>$form->createView(),'beneficiaries'=>$possibleBeneficiaries));
    }

    /**
     *Edit an existing beneficiary
     *
     * Only the ICC can be changed. Then, this new beneficiary is verified : 
     *  _checks that the user's beneficiary has an account with the provided ICC
     *  _check that new beneficiary is not already a beneficiary
     *@param Beneficiary $beneficiary Beneficiary with a given ICC is edited
     *@Method("GET")
     */
    public function editBeneficiaryAction(Request $request, Beneficiary $beneficiary, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');

        $newBeneficiary = new Beneficiary();
        $newBeneficiary->setUser($beneficiary->getUser());
        $newBeneficiary->setICC($beneficiary->getICC());
        $form = $this->createForm(BeneficiaryType::class,$newBeneficiary);
        $currentUser = $this->getUser();

        if($request->isMethod('GET')){
            $session->set('formerICC',$beneficiary->getICC());
        }
        if($request->isMethod('POST')){ //form filled and submitted            
            $formerBeneficiary = $beneficiaryRepo->findOneBy(array('ICC'=>$session->get('formerICC')));
            //            $session->remove('formerICC');
            $form->handleRequest($request);                                    
            if($form->isValid()){                                              
                //check that ICC exists and corresponds to this user
                $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($newBeneficiary->getICC());
                if(!$toUserVO){
                    $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte');
                    return new RedirectResponse($request->getRequestUri());
                }else{
                    $benefUser = $newBeneficiary->getUser();
                    if(! ($benefUser->getUsername() == $toUserVO->username)){
                        $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte de ' .$benefUser->getName());
                        return new RedirectResponse($request->getRequestUri());
                    }
                }


                $existingBeneficiary = $beneficiaryRepo->findOneBy(array('ICC'=>$newBeneficiary->getICC()));
                if($existingBeneficiary){
                    $newBeneficiary = $existingBeneficiary;
                }

                if($currentUser->hasBeneficiary($newBeneficiary)){
                    $session->getFlashBag()->add('info','Ce compte fait déjà partie de vos bénéficiaires.');
                    return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
                }

                $nbSources = count($formerBeneficiary->getSources()) ;
                $formerBeneficiary->removeSource($currentUser);
                $currentUser->removeBeneficiary($formerBeneficiary);
                if($nbSources == 1){
                    $em->remove($formerBeneficiary);
                }
                $currentUser->addBeneficiary($newBeneficiary);
                $newBeneficiary->addSource($currentUser);

                $em->persist($newBeneficiary);
                $em->persist($currentUser);
                $em->flush();
                $session->getFlashBag()->add('success','Modification effectuée avec succès');
                return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
            }                                                              
        }                                                                  

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView()));
        }
        return $this->render('CairnUserBundle:Pro:confirm_edit_beneficiary.html.twig',array('form'=>$form->createView()));
    }


    /**
     * Removes a given beneficiary
     *
     * Once $beneficiary is removed, we ensure that this beneficiary is associated to at least one user. Otherwise, it is removed
     * @param Beneficiary $beneficiary Beneficiary to remove
     * @Method("GET")
     */
    public function removeBeneficiaryAction(Request $request, Beneficiary $beneficiary, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConfirmationType::class);
        $currentUser = $this->getUser();

        if(!$currentUser->hasBeneficiary($beneficiary)){
            $errorMessage = 'Donnée introuvable';

            if($this->get('cairn_user.api')->isRemoteCall()){
                return $this->get('cairn_user.api')->getErrorResponse(array($errorMessage) ,Response::HTTP_BAD_REQUEST);
            }

            $session->getFlashBag()->add('error',$errorMessage);
            return $this->redirectToRoute('cairn_user_beneficiaries_list');
        }
        if($request->isMethod('DELETE') || $request->isMethod('POST')){ //form filled and submitted 
            if($_format == 'json'){
                $form->submit(json_decode($request->getContent(), true));
            }else{
                $form->handleRequest($request);
            }

            if($form->isValid()){                                              
                if($form->get('save')->isClicked()){ 
                    $nbSources = count($beneficiary->getSources());
                    $beneficiary->removeSource($currentUser);
                    $currentUser->removeBeneficiary($beneficiary);
                    if($nbSources == 1){
                        $em->remove($beneficiary);
                    }

                    $em->flush();
                    $flashMessage = 'Suppression effectuée avec succès';
                    $sessionKey = 'success';
                }                                                              
                else{
                    $flashMessage = 'Suppression annulée';
                    $sessionKey = 'info';
                }

                if($this->get('cairn_user.api')->isRemoteCall()){
                    $response = new Response('{ "message":"'.$flashMessage.'"}');
                    $response->setStatusCode(Response::HTTP_OK);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $session->getFlashBag()->add($sessionKey,$flashMessage);
                return $this->redirectToRoute('cairn_user_beneficiaries_list');
            }                                                                  
        }        

        return $this->render('CairnUserBundle:Pro:confirm_remove_beneficiary.html.twig',
            array(
                'form'=>$form->createView(),
                'beneficiary_name'=>$beneficiary->getUser()->getName()
            ));
    }

}

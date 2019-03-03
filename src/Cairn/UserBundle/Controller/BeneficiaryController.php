<?php

namespace Cairn\UserBundle\Controller;

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
 * This class contains all actions related to user experience
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
    public function listBeneficiariesAction(Request $request)
    {
        $beneficiaries = $this->getUser()->getBeneficiaries();

        if($this->get('cairn_user.api')->isApiCall()){
            $array_beneficiaries = array();

            foreach($beneficiaries as $beneficiary){
                $array_beneficiaries[] = $this->get('cairn_user.api')->serialize($beneficiary);
            }

            $response = new Response(json_encode($array_beneficiaries));
            $response->headers->set('Content-Type', 'application/json');
            $response->setStatusCode(Response::HTTP_OK);
            return $response;
        }

        return $this->render('CairnUserBundle:Pro:list_beneficiaries.html.twig',array('beneficiaries'=>$beneficiaries));
    }


    /**
     * Checks if the beneficiary is valid
     *
     * First, we check that the user matches the account number $ICC.
     * Then, we ensure that beneficiary exists in database, and whether he is a current beneficiary of $user or not
     *
     *@param User $user User who is supposed to own the account
     *@param int $ICC account cyclos ID

     *@return stdClass with attributes : isValid and error message if not
     */
    public function isValidBeneficiary($dataForm)
    {
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $response = new \stdClass();
        $response->isValid = true;


        $user = $userRepo->findOneBy(array('email'=>$dataForm['email']));
        if(!$user){
            $user = $userRepo->findOneBy(array('name'=>$dataForm['name']));
            if(!$user){
                $response->isValid = false;
                $response->errorMessage = 'Votre recherche ne correspond à aucun membre';
                return $response;
            }
        }

        $ICC = preg_replace('/\s+/', '', $dataForm['ICC']);

        $currentUser = $this->getUser();

        if($user === $currentUser){
            $response->isValid = false;
            $response->errorMessage = 'Vous ne pouvez pas vous ajouter vous-même...';
            return $response;
        }

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($ICC);
        if( (!$toUserVO) || ($toUserVO->id != $user->getCyclosID()) ){
            $response->isValid = false;
            $response->errorMessage = 'L\'identifiant indiqué ne correspond à aucun compte de' . $user->getName();
            return $response;
        }

        $existingBeneficiary = $beneficiaryRepo->findOneBy(array('user'=>$user,'ICC'=>$ICC));

        if($existingBeneficiary && $this->getUser()->hasBeneficiary($existingBeneficiary)){
            $response->isValid = false;
            $response->errorMessage = $user->getName().' est déjà votre un bénéficiaire enregistré ';
            return $response;
        }

        $response->existingBeneficiary = $existingBeneficiary;
        $response->user = $user;
        $response->ICC = $ICC;

        return $response;
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
    public function addBeneficiaryAction(Request $request)
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
            ->add('name', TextType::class, array('label' => 'Nom du bénéficiaire'))
            ->add('email', EmailType::class, array('label' => 'Email du bénéficiaire'))
            //ICC : IntegerType does not work for bigint : rounding after 14 figures (Account Ids in Cyclos have 19)
            ->add('ICC',   TextType::class,array('label'=>'Identifiant de Compte Cairn(ICC)'))
            ->add('add', SubmitType::class, array('label' => 'Ajouter'))
            ->getForm();

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $dataForm = $form->getData();

                //                $re_email ='#^[a-z0-9._-]+@[a-z0-9._-]{2,}\.[a-z]{2,4}$#' ;
                //                $re_name ='#^[\w\.]+$#' ;
                //                $re_ICC = '#^[-]?[0-9]+$#';
                //                preg_match_all($re_email,$dataForm['email'], $matches_email, PREG_SET_ORDER, 0);
                //                preg_match_all($re_name, $dataForm['name'], $matches_name, PREG_SET_ORDER, 0);
                //                preg_match_all($re_ICC, $dataForm['ICC'], $matches_ICC, PREG_SET_ORDER, 0);


                $result = $this->isValidBeneficiary($dataForm);
                if(! $result->isValid){
                    if( $this->get('cairn_user.api')->isApiCall()){
                        $response = new Response($result->errorMessage);
                        $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                        $response->headers->set('Content-Type', 'application/json');
                        return $response;
                    }
                    $session->getFlashBag()->add('error',$result->errorMessage);
                    return new RedirectResponse($request->getRequestUri());
                }


                //check that beneficiary is not already in database, o.w create new one
                $existingBeneficiary = $result->existingBeneficiary;

                if(! $existingBeneficiary){
                    $beneficiary = new Beneficiary();
                    $beneficiary->setUser($result->user);
                    $beneficiary->setICC($result->ICC);
                }
                else{ 
                    $beneficiary = $existingBeneficiary;
                }

                $beneficiary->addSource($currentUser);
                $currentUser->addBeneficiary($beneficiary);
                $em->persist($beneficiary);
                $em->persist($currentUser);
                $em->flush();

                if( $this->get('cairn_user.api')->isApiCall()){
                    $response = new Response('new beneficiary : success !');
                    $response->setStatusCode(Response::HTTP_CREATED);
                    $response->headers->set('Content-Type', 'application/json');
                    return $response;
                }

                $session->getFlashBag()->add('success','Nouveau bénéficiaire ajouté avec succès');
                return $this->redirectToRoute('cairn_user_beneficiaries_list');
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
     * @TODO : try the option OrphanRemoval in annotations to let Doctrine do it 
     * @param Beneficiary $beneficiary Beneficiary to remove
     * @Method("GET")
     */
    public function removeBeneficiaryAction(Request $request, Beneficiary $beneficiary)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConfirmationType::class);
        $currentUser = $this->getUser();

        if(!$currentUser->hasBeneficiary($beneficiary)){
            if($this->get('cairn_user.api')->isApiCall()){
                $response = new Response('Donnée introuvable');
                $response->setStatusCode(Response::HTTP_BAD_REQUEST);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
            }

            $session->getFlashBag()->add('error',' Donnée introuvable');
            return $this->redirectToRoute('cairn_user_beneficiaries_list');
        }
        if($request->isMethod('POST')){ //form filled and submitted            

            $form->handleRequest($request);                                    
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

                if($this->get('cairn_user.api')->isApiCall()){
                    $response = new Response($flashMessage);
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

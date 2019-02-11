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
class UserController extends Controller
{
    private $userManager;
    //    private $scriptManager;

    public function __construct()
    {
        $this->userManager = new UserManager();
    }

    public function indexAction(Request $request, $_format)
    {
        $checker = $this->get('security.authorization_checker');

        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();
        //last pros registered
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $qb = $userRepo->createQueryBuilder('u')
            ->orderBy('u.creationDate','DESC')
            ->andWhere('u.enabled = true')
            ->setMaxResults(5);
        $userRepo->whereRole($qb,'ROLE_PRO');
        $users =  $qb->getQuery()->getResult();

        //accounts of current user
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($currentUser);

        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id);

        $accountNumbers = $this->get('cairn_user_cyclos_account_info')->getAccountNumbers($ownerVO->id);

        //last operations
        $ob = $operationRepo->createQueryBuilder('o');
        $processedTransactions = $ob->where(
             $ob->expr()->orX(
                 $ob->expr()->andX(
                     $ob->expr()->in('o.fromAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',Operation::getExecutedTypes())
                 ),
                 $ob->expr()->andX(
                     $ob->expr()->in('o.toAccountNumber', $accountNumbers),
                     $ob->expr()->in('o.type',Operation::getExecutedTypes())
                 )
             ))
            ->andWhere('o.paymentID is not NULL')
            ->orderBy('o.executionDate','ASC')
            ->setMaxResults(15)
            ->getQuery()->getResult();

        if($checker->isGranted('ROLE_ADHERENT')){
            if($_format == 'json'){
                $response = $this->get('serializer')->serialize($users[0], 'json');
                //$response = array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions, 'lastUsers'=>$users);
                $response = new Response($response);
                $response->headers->set('Content-Type', 'application/json');
                return $response;
                return $this->json($response);
            }
            return $this->render('CairnUserBundle:User:index.html.twig',array('accounts'=>$accounts,'lastTransactions'=>$processedTransactions,'lastUsers'=>$users));
        }

    }

    /**
     *Get the list of all users grouped by roles
     *
     */
    public function listUsersAction(Request $request, $_format)
    {
        $currentUserID = $this->getUser()->getID();
        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');

        $pros = new \stdClass();
        $pros->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',true);
        $pros->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PRO',false);
        $pros->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PRO');
        $pros->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PRO');

        $persons = new \stdClass();
        $persons->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',true);
        $persons->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_PERSON',false);
        $persons->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_PERSON');
        $persons->nocard = $userRepo->findUsersWithPendingCard($currentUserID,'ROLE_PERSON');

        $admins = new \stdClass();
        $admins->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',true);
        $admins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_ADMIN',false);
        $admins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_ADMIN');

        $superAdmins = new \stdClass();
        $superAdmins->enabled = $userRepo->findUsersWithStatus($currentUserID,'ROLE_SUPER_ADMIN',true);
        $superAdmins->blocked = $userRepo->findUsersWithStatus($currentUserID,'ROLE_SUPER_ADMIN',false);
        $superAdmins->pending = $userRepo->findPendingUsers($currentUserID,'ROLE_SUPER_ADMIN');

        $allUsers = array(
            'pros'=>$pros, 
            'persons'=>$persons,
            'admins'=>$admins,
            'superAdmins'=>$superAdmins,
        );

        if($_format == 'json'){
            return $this->json($allUsers);
        }
        return $this->render('CairnUserBundle:User:list_users.html.twig',$allUsers);
    }


//    public function getAction(Request $request, User $user)
//    {
//
//    }
    /**
     * List API options related to user URI 
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
        $options['get users list'] = array(
            'notes'=> 'Request list of users',
            'path'=> 'api/users',
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
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'you do not have access to these users'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['get user profile'] = array(
            'notes'=> 'Request profile data for an user with id {id}',
            'path'=> "api/users/{id}",
            'method'=> 'GET',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
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
                'undefined user'=> array(
                     'status code'=> '404',
                     'reason'=> 'no user with given id'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['post'] = array(
            'notes'=> 'Create a new user using provided data',
            'path'=> 'api/users',
            'method'=> 'POST',
            'Parameters'=> array(
                'query'=>array(
                    'type'=>array(
                        'description'=> 'kind of user to add',
                        'data_type'=> 'string',
                        'values'=> array('pro','localGroup','superAdmin')
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name (required)',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login (required)',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address (required)',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city (required)',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city (required)',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> array('image/jpeg','image/jpg','image/png','image/gif')
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '201',
                     'reason'=> 'successful user creation'
                ),
                'access denied'=> array(
                     'status code'=> '403',
                     'reason'=> 'Given current user\'s role, creating a user is not authorized'
                 ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );        

        $options['put'] = array(
            'notes'=> ' Update user with id {id} using provided properties',
            'path'=> 'api/users/{id}',
            'method'=> 'PUT',
            'Parameters'=> array(
                'query'=>array(
                    'id'=>array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    )
                ),
                'body'=>array(
                    'name'=>array(
                        'description'=> 'User name',
                        'data_type'=> 'string'
                    ),
                    'username'=>array(
                        'description'=> 'User login',
                        'data_type'=> 'string'
                    ),
                    'address'=>array(
                        'address1'=> array(
                            'description'=> 'User main address',
                            'data_type'=> 'string'
                        ),
                        'address2'=> array(
                            'description'=> 'User complement address',
                            'data_type'=> 'string'
                        ),
                        'city'=> array(
                            'description'=> 'User current city',
                            'data_type'=> 'string'
                        ),
                        'zipcode'=> array(
                            'description'=> 'Zipcode of the city',
                            'data_type'=> 'int'
                        ),

                    ),
                    'description'=>array(
                        'description'=> 'Provide a few words on user\'s activity',
                        'data_type'=> 'text'
                    ),
                    'logo'=>array(
                        'description'=> 'User avatar',
                        'data_type'=> 'image',
                        'mimeTypes'=> 'image/jpeg','image/jpg','image/png','image/gif'
                    )
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );

        $options['delete'] = array(
            'notes'=> 'Request delete user with id {id}',
            'path'=> 'api/users/{id}',
            'method'=> 'DELETE',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                      'description'=> 'user id (required)',
                      'data_type'=> 'int'
                  )
                ),
                'body'=>array()
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user update'
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['Change_password'] = array(
            'notes'=> 'Change password',
            'path'=> 'api/users/change-password',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=> array(),
                'body'=> array(
                    'current password'=> array(
                      'description'=> 'User current password (required)',
                      'data_type'=> 'string'
                    ),
                    'new password'=> array(
                      'description'=> 'User requested password (required)',
                      'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                      'description'=> 'Confirm requested password (required)',
                      'data_type'=> 'string'
                    ),
                ),
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful password change'
                ),
                'error'=> array(
                     'status code'=> '500',
                     'reason'=> 'API Internal error'
                )
            )
        );
        $options['associate_card'] = array(
            'notes'=> 'Associate a security card to a user',
            'path'=> 'api/users/{id}/associate-card',
            'method'=> 'PATCH',
            'Parameters'=> array(
                'query'=>array(
                    'id'=> array(
                        'description'=> 'user id (required)',
                        'data_type'=> 'int'
                    ),
                ),
                'body'=>array(
                    'current password'=> array(
                        'description'=> 'User current password (required)',
                        'data_type'=> 'string'
                    ),
                    'new password'=> array(
                        'description'=> 'User requested password (required)',
                        'data_type'=> 'string'
                    ),
                    'confirm new password'=> array(
                        'description'=> 'Confirm requested password (required)',
                        'data_type'=> 'string'
                    ),
             
                )
            ),
            'Response messages'=> array(
                'success'=> array(
                     'status code'=> '200',
                     'reason'=> 'successful user creation'       
                ),
                'access denied'=> array(
                 'status code'=> '403',
                 'reason'=> 'forbidden removal on given user => not referent or not the user himself'
                ),
                'error'=> array(
                      'status code'=> '500',
                     'reason'=> 'error on server-side'           
                ),
            )
        );

        return new Response(json_encode($options));
    }                      

    /**
     * View the profile of $user
     *
     * What will be displayed on the screen will depend on the current user
     *
     * @param User $user User with profile to view
     */
    public function viewProfileAction(Request $request , User $user, $_format)                           
    {                                                                          
        $currentUser = $this->getUser();
        if( (!$this->get('security.authorization_checker')->isGranted('ROLE_ADMIN')) && $user->hasRole('ROLE_SUPER_ADMIN')){
            throw new AccessDeniedException('Pas les droits nécessaires pour accéder au profil de cet utilisateur');
        } 

        if($_format == 'json'){
            $serializedUser = $this->get('cairn_user.api')->serialize($user, array('localGroupReferent','singleReferent','referents','beneficiaries','card'));
            $response = new Response($serializedUser);
            $response->headers->set('Content-Type', 'application/json');
            return $response;
        }
        return $this->render('CairnUserBundle:Pro:view.html.twig', array('user'=>$user));
    }                      

    /**
     * Get the list of beneficiaries for current User
     *
     */
    public function listBeneficiariesAction(Request $request, $_format)
    {
        $beneficiaries = $this->getUser()->getBeneficiaries();

        if($_format == 'json'){
            return $this->json(array('beneficiaries'=>$beneficiaries));
        }
        return $this->render('CairnUserBundle:Pro:list_beneficiaries.html.twig',array('beneficiaries'=>$beneficiaries));
    }



    /**
     * Checks if the beneficiary exists in database, and is a current beneficiary of $user
     *
     *@param User $user User who is supposed to own the account
     *@param int $ICC account cyclos ID

     *@return stdClass with attributes : 'existingBeneficiary'(Beneficiary class) and 'hasBeneficiary'(boolean)
     */
    public function isValidBeneficiary($user, $ICC)
    {
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $toReturn = new \stdClass();

        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        $toReturn->account = $this->get('cairn_user_cyclos_account_info')->hasAccount($ownerVO->id,$ICC);

        $existingBeneficiary = $beneficiaryRepo->findOneBy(array('user'=>$user,'ICC'=>$ICC));

        if($existingBeneficiary){
            $toReturn->existingBeneficiary = $existingBeneficiary;
            $toReturn->hasBeneficiary = $this->getUser()->hasBeneficiary($existingBeneficiary);
        }
        else{
            $toReturn->existingBeneficiary = NULL;
            $toReturn->hasBeneficiary = NULL; 
        }
        return $toReturn;
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
    public function addBeneficiaryAction(Request $request, $_format)
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

                $user = $userRepo->findOneBy(array('email'=>$dataForm['email']));
                if(!$user){
                    $user = $userRepo->findOneBy(array('name'=>$dataForm['name']));
                    if(!$user){
                        $session->getFlashBag()->add('error','Votre recherche ne correspond à aucun membre');
                        return new RedirectResponse($request->getRequestUri());

                    }
                }
                if($user->getID() == $currentUser->getID())
                {
                    $session->getFlashBag()->add('error','Vous ne pouvez pas vous ajouter vous-même...');
                    return new RedirectResponse($request->getRequestUri());
                }
                $ICC = preg_replace('/\s+/', '', $dataForm['ICC']);

                //check that ICC exists and corresponds to this user
                $toUserVO = $this->get('cairn_user_cyclos_user_info')->getUserVOByKeyword($ICC);
                if(!$toUserVO){
                    $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte');
                    return new RedirectResponse($request->getRequestUri());
                }else{
                    if(! ($user->getUsername() == $toUserVO->username)){
                        $session->getFlashBag()->add('error','L\' ICC indiqué ne correspond à aucun compte de ' .$user->getName());
                        return new RedirectResponse($request->getRequestUri());

                    }
                }

                //check that beneficiary is not already in database, o.w create new one
                $existingBeneficiary = $beneficiaryRepo->findOneBy(array('ICC'=>$ICC));

                if(!$existingBeneficiary){
                    $beneficiary = new Beneficiary();
                    $beneficiary->setUser($user);
                    $beneficiary->setICC($ICC);
                }
                else{ 
                    if($currentUser->hasBeneficiary($existingBeneficiary)){
                        $session->getFlashBag()->add('info','Ce compte fait déjà partie de vos bénéficiaires.');
                        return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
                    }
                    $beneficiary = $existingBeneficiary;
                }

                $beneficiary->addSource($currentUser);
                $currentUser->addBeneficiary($beneficiary);
                $em->persist($beneficiary);
                $em->persist($currentUser);
                $em->flush();
                $session->getFlashBag()->add('success','Nouveau bénéficiaire ajouté avec succès');
                return $this->redirectToRoute('cairn_user_beneficiaries_list', array('_format'=>$_format));
            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'beneficiaries'=>$possibleBeneficiaries));
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
    public function removeBeneficiaryAction(Request $request, Beneficiary $beneficiary, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $form = $this->createForm(ConfirmationType::class);
        $currentUser = $this->getUser();

        if(!$currentUser->hasBeneficiary($beneficiary)){
            $session->getFlashBag()->add('error',' Donnée introuvable');
            return $this->redirectToRoute('cairn_user_beneficiaries_list',array('_format'=>$_format));
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
                    $session->getFlashBag()->add('success','Suppression effectuée avec succès');

                    //TODO here
                }                                                              
                else{
                    $session->getFlashBag()->add('info','Suppression annulée');
                }
                return $this->redirectToRoute('cairn_user_beneficiaries_list',array('_format'=>$_format));
            }                                                                  
        }        
        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'beneficiary_name'=>$beneficiary->getUser()->getName()));
        }

        return $this->render('CairnUserBundle:Pro:confirm_remove_beneficiary.html.twig',
            array(
                'form'=>$form->createView(),
                'beneficiary_name'=>$beneficiary->getUser()->getName()
            ));
    }


    /**
     *Removes a user with id given in query
     *
     * This operation is considered as super sensible. It needs the security layer + the password input
     * If the input is incorrect, user's attribute 'cardKeyTries' or 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     * A user can remove its own member area, or an admin can do it if he is a referent.
     *
     * If the user to remove is a ROLE_ADHERENT, we ensure that all his accounts have a balance to zero
     *
     * @Method("GET")
     *
     * Can only be done by an admin on Cyclos side. 
     * Solution 1 : create a specific screen for all users with attribute "ask_removal = true" and remove them all in once in admin's
     * action. 
     *
     * Problem : If a user asks for removal and is accepted, it means that all his accounts are at 0. But if he receives a payment
     * between this and validation by admin, his account won't be 0 so he will not be removed. But there is no way to check (from an ICC provided) that the corresponding Symfony User has this attrbute, because an user can't look for the account of soemone else.
     *
     * Solution 1 : récupérer un compte via un account_number (configurer un account_number : script de génération +  service d'interception 'création de compte' + visiblité dans les Produits). Ainsi on pourrait récupérer, à partir du account_number, le propriétaire du compte puis son équivalent Symfony pour dire s'il est en instance de suppression ou non. (Long car beaucoup de nouvelles choses à voir)
     *
     * Solution 2 : vérifier le message d'erreur à la suppression(ConstraintViolatedOnRemoveException) et, si la suppression ne peut avoir lieu à cause d'un compte non nul, avertir l'utilisateur
     *
     */
    public function confirmRemoveUserAction(Request $request, User $user, $_format)
    {
        $isAdmin = $this->get('security.authorization_checker')->isGranted('ROLE_ADMIN'); 

        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();
        $currentUser = $this->getUser();

        $userRepo = $em->getRepository('CairnUserBundle:User');

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        //check that account balances are all 0 (for PRO only)
        $ownerVO = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);
        $accounts = $this->get('cairn_user_cyclos_account_info')->getAccountsSummary($ownerVO->id,NULL);

        if($user->hasRole('ROLE_PRO') || $user->hasRole('ROLE_PERSON')){
            foreach($accounts as $account){
                if($account->status->balance != 0){
                    $session->getFlashBag()->add('error','Certains comptes ont un solde non nul. La suppression ne peut aboutir.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'id' => $user->getID()));
                }
            }
        }

        $form = $this->createForm(ConfirmationType::class);
        $form->add('current_password', PasswordType::class,array('label'=>'Mot de passe','required'=>false,
            'constraints'=> new UserPassword() ));
        if($request->isMethod('POST')){ //form filled and submitted

            $form->handleRequest($request);    
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    if($isAdmin){
                        $redirection = 'cairn_user_users_home';
                        $isRemoved = $this->removeUser($user, $currentUser);
                        $session->getFlashBag()->add('success','Espace membre supprimé avec succès');
                    }else{//is ROLE_PRO or ROLE_PERSON
                        $user->setRemovalRequest(true);
                        $user->setEnabled(false);

                        $redirection = 'fos_user_security_logout';
                        $session->getFlashBag()->add('success','Votre demande de suppression d\'espace membre a été prise en compte');
                    }
                    $em->flush();

                    return $this->redirectToRoute($redirection);
                }
                else{
                    $session->getFlashBag()->add('info','La demande de suppression a été annulée.');
                    return $this->redirectToRoute('cairn_user_profile_view',array('_format'=>$_format,'id'=> $user->getID()));
                }
            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'user'=>$user));
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove.html.twig',array('form'=>$form->createView(),'user'=>$user));
    }

    /**
     *Does remove the user on both the Cyclos and Doctrine sides and sends email to removed user and their referents
     *
     * It removes the user from Doctrine and all related Beneficiary entities with it.
     *@param User $user User to be removed
     *@todo : test query builder request using a join to get beneficiaries associated to user to see if it is faster
     */
    public function removeUser(User $user, User $currentUser)
    {
        $em = $this->getDoctrine()->getManager();
        $beneficiaryRepo = $em->getRepository('CairnUserBundle:Beneficiary');
        $operationRepo = $em->getRepository('CairnUserBundle:Operation');

        $messageNotificator = $this->get('cairn_user.message_notificator');

        $referents = $user->getReferents();

        $saveName = $user->getName();

        $params = new \stdClass();
        $params->status = 'REMOVED';
        $params->user = $this->get('cairn_user.bridge_symfony')->fromSymfonyToCyclosUser($user);

        try{
            $this->userManager->changeStatusUser($params);
            $emailTo = $user->getEmail();

            //remove beneficiaries associated to the user to remove
            $beneficiaries = $beneficiaryRepo->findBy(array('user'=>$user));
            foreach($beneficiaries as $beneficiary){
                $em->remove($beneficiary);
            }
            
            //TODO : ONE single SQL insert request instead of the two here
            //set Operations with user to remove as creditor/debitor to NULL
            $operations = $operationRepo->findBy(array('creditor'=>$user));
            foreach($operations as $operation){
                $operation->setCreditor(NULL);
            }

            $operations = $operationRepo->findBy(array('debitor'=>$user));
            foreach($operations as $operation){
                $operation->setDebitor(NULL);
            }

            $em->remove($user);

            $subject = 'Ce n\'est qu\'un au revoir !';
            $from = $messageNotificator->getNoReplyEmail();
            $to = $emailTo;
            $body = $this->renderView('CairnUserBundle:Emails:farwell.html.twig');

            $messageNotificator->notifyByEmail($subject,$from,$to,$body);

            $subject = 'Un professionnel a été supprimé de la plateforme';
            $body = $saveName .' a été supprimé de la plateforme par '. $currentUser->getName();
            foreach($referents as $referent){
                $to = $referent->getEmail();
                $messageNotificator->notifyByEmail($subject,$from,$to,$body);
            }

            return true;

        }catch(Cyclos\ServiceException $e){
            if($e->errorCode == 'VALIDATION'){
                $subject = 'Demande de suppression annulée';
                $from = $messageNotificator->getNoReplyEmail();
                $to = $user->getEmail();
                $body = 'Vous avez demandé à supprimer votre espace membre, mais certains de vos comptes ont un solde non nul. Elle n\'a donc pas pu être validée. Mettez vos comptes à 0, et vérifiez que vous ne recevez pas des virements entre la demande et la validation';//$this->renderView('CairnUserBundle:Emails:farwell.html.twig');

                $messageNotificator->notifyByEmail($subject,$from,$to,$body);
                return false;

            }else{
                throw $e;
            }
        }

    }

    /**
     * Removes definitively users that requested to be removed
     *
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function removePendingUsersAction(Request $request, $_format)
    {
        $session = $request->getSession();

        $em = $this->getDoctrine()->getManager();
        $userRepo = $em->getRepository('CairnUserBundle:User');
        $currentUser = $this->getUser();

        //members on pending removal status
        $ub = $userRepo->createQueryBuilder('u');
        $userRepo->whereReferent($ub, $currentUser->getID());
        $listUsers = $ub
            ->andWhere('u.removalRequest = true')
            ->orderBy('u.name','ASC')
            ->getQuery()->getResult(); 

        $form = $this->createForm(ConfirmationType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            if($form->get('save')->isClicked()){
                $notRemovedUsers = '';
                foreach($listUsers as $user){
                    $isRemoved = $this->removeUser($user, $currentUser);
                    if(!$isRemoved){
                        $user->setEnabled(true);
                        $user->setRemovalRequest(false);
                        $notRemovedUsers = $notRemovedUsers.', '.$user->getName();
                    }
                }

                $em->flush();

                if($notRemovedUsers != ''){
                    $session->getFlashBag()->add('info','Les membres suivants n\'ont pas pu être supprimés : ' .$notRemovedUsers); 
                }else{
                    $session->getFlashBag()->add('success','Tous les membres ont pas pu être supprimés avec succès'); 
                }
                return $this->redirectToRoute('cairn_user_users_home', array('_format'=>$_format));

            }else{
                $session->getFlashBag()->add('info','Opération annulée'); 
                return $this->redirectToRoute('cairn_user_users_home', array('_format'=>$_format));
            }
        }
        return $this->render('CairnUserBundle:Pro:confirm_remove_pending.html.twig',
            array('form'=>$form->createView(),'listUsers'=>$listUsers));

    }

}

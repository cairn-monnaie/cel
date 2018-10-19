<?php

namespace Cairn\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage Events 
use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\Event\InputPasswordEvent;
use Cairn\UserBundle\Event\InputCardKeyEvent;

//manage Entities
use Cairn\UserBundle\Entity\User;
use Cairn\UserBundle\Entity\Card;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

//manage Forms
use Cairn\UserBundle\Form\CardType;
use Cairn\UserBundle\Form\ConfirmationType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;


/**
 * This class contains all actions related to security cards
 *
 * @Security("is_granted('ROLE_PRO')")
 */
class CardController extends Controller
{

    /**
     * Generates a random array index and its translated position as a cell
     *
     * @example For a 5x5 card. Index 7 gives cell position B2
     * @param Card $card
     * @return stdClass with attributes cell and index 
     */
    public function generatePositions(Card $card)
    {
        $rows = $card->getRows();
        $nbFields = $rows * $card->getCols();
        $position = rand(0,$nbFields-1);
        $pos_row = intdiv($position,$rows);                                    
        $pos_col = $position % $rows;
        $array_pos = chr(65+ $pos_row) . strval($pos_col + 1); 

        return ['cell' => $array_pos ,'index'=>$position];
    }

    /**
     * The user must input a key of his card in order to keep browsing
     *
     * This action is used as a security layer to ensure user's identity. If the input is incorrect, user's attribute 'cardKeyTries'
     * is incremented. 3 failures leads to disable the user.
     *
     * This action is called only on sensible operations, whose routes and URLs(if route is not enough but the query must be considered)
     * are defined in the SecurityEvents class. 
     * Of course, the user's card must exist and be active, otherwise no sensible operation can be operated.
     *
     * For all sensible operations being listed in the SecurityEvent class, controller actions must contain query parameters in the query 
     * array and not in the route itself, as the query is passed as a query to this function before reaching the sensible 
     * initial route. Otherwise, for instance, if {id} was passed in the route, it wouldn't be available from here, and it would be 
     * impossible to reach the initial request.
     *
     * @see \Cairn\UserBundle\Event\SecurityEvents
     */
    public function inputCardKeyAction(Request $request)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $url = $request->query->get('url');
        $currentUser = $this->getUser();

        $card = $currentUser->getCard();

        if(!$card){
            $session->getFlashBag()->add('info','Vous n\'avez pas de carte de sécurité Cairn. Votre opération ne peut être poursuivie. Commandez-en une');
            return $this->redirectToRoute('cairn_user_card_home',array('id'=>$currentUser->getID()));
        }
        if(!$card->isEnabled()){
            $session->getFlashBag()->add('info','Votre carte courante n\'est pas active. Votre opération ne peut être poursuivie. Activez-la.');
            return $this->redirectToRoute('cairn_user_card_home',array('id'=>$currentUser->getID()));
        }

        $positions = $this->generatePositions($card);
        if($request->isMethod('GET')){
            $session->set('position',$positions['index']);
        }
        $array_pos = $positions['cell'];

        $form = $this->createForm(CardType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $position = $session->get('position');
                $cardKey =  $form->get('field')->getData();

                $event = new InputCardKeyEvent($currentUser,$cardKey,$position, $session);
                $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_CARD_KEY,$event);

                if($event->getRedirect()){
                    $session->getFlashBag()->add('error','Votre compte a été bloqué');
                    return $this->redirectToRoute('fos_user_security_logout');
                }

                $nbTries = $currentUser->getCardKeyTries();
                if($nbTries == 0){
                    return new RedirectResponse($url);
                }
                else{
                    $session->getFlashBag()->add('error','Clé invalide. Veuillez réessayer');
                    return $this->redirectToRoute('cairn_user_card_security_layer',array('url'=>$url));
                }

            }
        }
        return $this->render('CairnUserBundle:Card:validate_card.html.twig',array('form'=>$form->createView(),'card'=>$card,'position'=>$array_pos));
    }


    /**
     * Index page for cards' action. 
     *
     * A user can make an action for its own card, or must be a referent of the card's owner
     *
     *@param User $user card owner
     *@Method("GET")
     */  
    public function cardOperationsAction(Request $request,User $user, $_format)
    {
        $currentUser = $this->getUser();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        if($_format == 'json'){
            return $this->json(array('user'=>$user));
        }
        return $this->render('CairnUserBundle:Card:card_operation.html.twig',array('user'=>$user));
    }

    /**
     * Requests for a new card
     *
     * To request for a new card, the current card of $user must have been revoked. This request can be done by the user itself, or 
     * by one of its referents. To ensure security, the user doing the request is asked to input its password. In case of failure,
     * user's attribute 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     *@param User $user User who needs a new card
     *@throws AccessDeniedException currentUser is not card's owner or referent of card's owner
     *@Method("GET")
     */
    public function newCardAction(Request $request, User $user, $_format)
    {
        $currentUser = $this->getUser();
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $card = $user->getCard();
        if($card){
            if($card->isEnabled()){
                $session->getFlashBag()->add('info','La carte courante est active. Vous ne pouvez commander une autre carte qu\'en cas de perte de la carte courante. Veuillez la révoquer d\'abord.');
            }else{
                $session->getFlashBag()->add('info','Vous avez déjà une carte courante, inactive. Veuillez l\'activer ou la révoquer en cas de perte.');
            }
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));
        }

        $form = $this->createForm(ConfirmationType::class);
        $form->add('password', PasswordType::class, array('label'=> 'Mot de passe','required'=>false));

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    $password = $form->get('password')->getData();

                    $event = new InputPasswordEvent($currentUser,$password);
                    $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_PASSWORD,$event);

                    if($event->getRedirect()){
                        $session->getFlashBag()->add('error','Votre compte a été bloqué');
                        return $this->redirectToRoute('fos_user_security_logout');
                    }

                    if($currentUser->getPasswordTries() == 0) {
                        $card = new Card($user,$this->getParameter('cairn_card_rows'), $this->getParameter('cairn_card_cols') );
                        $user->setCard($card);

                        $em->flush();
                        //email
                        $subject = 'Nouvelle carte de sécurité Cairn';
                        $from = $this->getParameter('cairn_email_noreply');
                        $to = $user->getEmail();

                        $body = $this->renderView('CairnUserBundle:Emails:new_card.html.twig',array('by'=>$currentUser,'user'=>$user));
                        $this->get('cairn_user.message_notificator')->notifyByEmail($subject,$from,$to,$body);

                        $session->getFlashBag()->add('success','Votre demande a bien été prise en compte. Un email a été envoyé à l\'adresse ' . $user->getEmail());
                    }
                    else{
                        $session->getFlashBag()->add('error','Mot de passe invalide.');
                        return new RedirectResponse($request->getRequestUri());
                    }
                }
                else{
                    $session->getFlashBag()->add('info','Vous avez annulé votre commande de carte.');

                }
                return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));

            }
        }
        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView()));
        }
        return $this->render('CairnUserBundle:Card:confirm_new_card.html.twig',array('form'=>$form->createView()));
    }

    /**
     * Requests for a card revocation
     *
     * To request for a card revocation, the current card of $user must exist. This request can be done by the user himself, or 
     * by one of his referents. To ensure security, the user doing the request is asked to input his password. In case of failure,
     * user's attribute 'passwordTries' is incremented. 3 failures leads to disable the user.
     *
     *@param User $user User whose card will be revoked
     *@throws AccessDeniedException currentUser is not card's owner or referent of card's owner
     *@Method("GET")
     */
    public function revokeCardAction(Request $request, User $user, $_format)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();

        $currentUser = $this->getUser();

        if(! (($user === $currentUser) || ($user->hasReferent($currentUser))) ){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $card = $user->getCard();
        if(!$card){
            $session->getFlashBag()->add('info','La carte de sécurité Cairn a déjà été révoquée. Vous pouvez en commander une nouvelle.');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format, 'id'=>$user->getID()));
        }
        if(!$card->getFields()){
                $session->getFlashBag()->add('error',
                    'La carte de sécurité n\'a pas encore été créée. Vous ne pouvez donc pas la révoquer.');
                return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));
        }

        $form = $this->createForm(ConfirmationType::class);
        $form->add('password', PasswordType::class, array('label'=> 'Mot de passe','required'=>false));

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                if($form->get('save')->isClicked()){
                    $password = $form->get('password')->getData();
                    $event = new InputPasswordEvent($currentUser,$password);
                    $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_PASSWORD,$event);

                    if($event->getRedirect()){
                        $session->getFlashBag()->add('error','Votre compte a été bloqué');
                        return $this->redirectToRoute('fos_user_security_logout');
                    }

                    if($currentUser->getPasswordTries() == 0){
                        $subject = 'Révocation de votre carte de sécurité Cairn';
                        $from = $this->getParameter('cairn_email_noreply');
                        $to = $user->getEmail();
                        $body = $this->renderView('CairnUserBundle:Emails:revoke_card.html.twig',array('by'=>$currentUser));

                        $this->get('cairn_user.message_notificator')->notifyByEmail($subject,$from,$to,$body);

                        $em->remove($card);
                        $em->flush();

                        $session->getFlashBag()->add('info','Votre demande a bien été prise en compte. Un email a été envoyé à l\'adresse ' . $user->getEmail());
                    }
                    else{
                        $session->getFlashBag()->add('error','Mot de passe invalide.');
                        return new RedirectResponse($request->getRequestUri()); 
                    }
                }
                else{
                    $session->getFlashBag()->add('info','Vous avez annulé la révocation de la carte n° ' .$card->getNumber());
                }
                return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));

            }
        }
        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'card'=>$card));
        }
        return $this->render('CairnUserBundle:Card:confirm_revoke_card.html.twig',array('form'=>$form->createView(),'card'=>$card));
    }


    /**
     * Validates new user's card
     *
     * To ensure security, the user is asked to input its card key. In case of failure, user's attribute 'cardKeyTries' is incremented. 
     * 3 failures leads to disable the user.
     */
    public function validateCardAction(Request $request, $_format)
    {
        $session = $request->getSession();
        $user = $this->getUser();
        $card = $user->getCard();
        $em = $this->getDoctrine()->getManager();

        if(!$card){
            $session->getFlashBag()->add('info','Votre carte de sécurité Cairn a été révoquée. Commandez-en une nouvelle.');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format, 'id'=>$user->getID()));
        }
        elseif(!$card->isGenerated()){
            $session->getFlashBag()->add('info','Votre carte de sécurité Cairn ne vous a pas été envoyé. Validation impossible');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format, 'id'=>$user->getID()));
        }
        if($card->isEnabled()){
            $session->getFlashBag()->add('info','Votre carte de sécurité Cairn est déjà active.');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));
        }

        $positions = $this->generatePositions($card);
        if($request->isMethod('GET')){
            $session->set('position',$positions['index']);
        }
        $array_pos = $positions['cell'];

        $form = $this->createForm(CardType::class);

        if($request->isMethod('POST')){
            $form->handleRequest($request);
            if($form->isValid()){
                $position = $session->get('position');
                $cardKey =  $form->get('field')->getData();

                $event = new InputCardKeyEvent($card->getUser(),$cardKey,$position, $session);
                $this->get('event_dispatcher')->dispatch(SecurityEvents::INPUT_CARD_KEY,$event);

                if($event->getRedirect()){
                    $session->getFlashBag()->add('error','Votre compte a été bloqué');
                    return $this->redirectToRoute('fos_user_security_logout');
                }


                if($user->getCardKeyTries() == 0){
                    $session->set('has_input_card_key_valid',false);
                    $card->setEnabled(true);
                    $em->flush();

                    $session->getFlashBag()->add('success','Votre carte a été activée avec succès.');
                    return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));

                }
                else{
                    $session->getFlashBag()->add('error','Clé invalide. Veuillez réessayer');
                    return new RedirectResponse($request->getRequestUri());
                }
            }
        }

        if($_format == 'json'){
            return $this->json(array('form'=>$form->createView(),'card'=>$card,'position'=>$array_pos));

        }
        return $this->render('CairnUserBundle:Card:validate_card.html.twig',array('form'=>$form->createView(),'card'=>$card,'position'=>$array_pos));

    }


    /**
     * Generates a new card entity, print it in PDF format then encode it in database
     *
     * This action is considered as a sensible operation.
     * Card generation can be done by an admin for user under its responsibility. An exception case is installed SUPER_ADMIN who can 
     * generate a card for himself
     *
     * The card is encoded in database using user's salt attribute to add a security layer in database.
     *
     *@Security("is_granted('ROLE_ADMIN')")
     *@Method("GET")
     */
    public function generateCardAction(Request $request, User $user, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $currentUser = $this->getUser();

        if(! $user->hasReferent($currentUser)){
            throw new AccessDeniedException('Vous n\'êtes pas référent de '. $user->getUsername() .'. Vous ne pouvez donc pas poursuivre.');
        }

        $card = $user->getCard();

        if(!$card){
            $session->getFlashBag()->add('info',$user->getName() . ' n\'a pas de carte de sécurité à générer. Commandez-en une nouvelle.');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));
        }
        if($card->isGenerated()){
            $session->getFlashBag()->add('info','La carte de sécurité a déjà été générée.');
            return $this->redirectToRoute('cairn_user_card_home',array('_format'=>$_format,'id'=>$user->getID()));
        }

        $form = $this->createForm(ConfirmationType::class);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            if($form->get('cancel')->isClicked()){
                return $this->redirectToRoute('cairn_user_card_home', array('_format'=>$_format,'id'=>$user->getID()));
            }

            $card->generateCard($this->getParameter('kernel.environment'));
            $em->flush();

            $session->getFlashBag()->add('success','Pensez à supprimer le fichier de votre ordinateur dès que la carte a été imprimée !');

            return $this->redirectToRoute('cairn_user_card_download', array('_format'=>$_format,'id'=>$card->getID()));

        }

        if($_format == 'json'){
            return $this->json(array(
                'user' => $user,
                'form'   => $form->createView()
            ));
        }
        return $this->render('CairnUserBundle:Card:generate.html.twig', array(
            'user' => $user,
            'form'   => $form->createView()
        ));

    }

    /*
     *@Method("GET")
     *
     */ 
    public function downloadCardAction(Request $request, Card $card, $_format)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $fields = $card->getFields();
        $user = $card->getUser();

        if(!$fields){
            $session->getFlashBag()->add('error',' Etape de vérification sautée. Petit Filou !');
            return $this->redirectToRoute('cairn_user_card_home', array('_format'=> $format, 'id'=>$card->getUser()->getID()));
        }

        $fields = unserialize($fields);

        $html =  $this->renderView('CairnUserBundle:Pdf:card.html.twig',
            array('card'=>$card,'fields'=>$fields));

        $card->setGenerated(true);

        //encoder la carte
        $encoder = $this->get('security.encoder_factory')->getEncoder($user);

        $nbRows = $card->getRows();
        $nbCols = $card->getCols();

        for($row = 0; $row < $nbRows; $row++){
            for($col = 0; $col < $nbCols; $col++){
                $encoded_field = $encoder->encodePassword($fields[$row][$col],$user->getSalt());
                $fields[$row][$col] = substr($encoded_field,0,4);
            }
        }

        $card->setFields(serialize($fields));

        $em->flush();
        $filename = sprintf('carte-sécurité-cairn-'.$card->getNumber().'-%s.pdf',$user->getUserName());

        if($_format == 'json'){
            return new JsonResponse(
                $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
                200,
                [
                    'Content-Type'        => 'application/pdf',
                    'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
                ]
            );
        }

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            200,
            [
                'Content-Type'        => 'application/pdf',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $filename),
            ]
        );
    }

    /**
     * searches users with unactivated cards, warns them or remove their card
     *
     * Everyday, this action is requested to look for users who have not activated their card. A maximal delay is defined.
     * If the deadline is missed, the new user's card is automatically removed with an email notification sent, otherwise he is just
     * reminded to validate it 5/2 and 1 day before the deadline
     *
     * @Security("has_role('ROLE_SUPER_ADMIN')")
     */
    public function checkCardsExpirationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        $cardRepo = $em->getRepository('CairnUserBundle:Card');

        $cb = $cardRepo->createQueryBuilder('c');
        $cb->join('c.user','u')
            ->where('c.enabled = false')
            ->andWhere('c.creationDate >= :date')
            ->setParameter('date',strtotime('-' .$this->getParameter('card_activation_delay').' days'))
            ->andWhere('c.generated = true')
            ->addSelect('u');
        $cards = $cb->getQuery()->getResult();

        $messageNotificator = $this->get('cairn_user.message_notificator');
        $from = $messageNotificator->getNoReplyEmail();

        $today = new \Datetime(date('Y-m-d H:i:s'));
        foreach($cards as $card){
            $creationDate = $card->getCreationDate();
            $expirationDate = date_modify(new \Datetime($creationDate->format('Y-m-d H:i:s')),'+ '.$this->getParameter('card_activation_delay').' days');
            $interval = $today->diff($expirationDate);
            $diff = $interval->days;
            $nbMonths = intdiv($this->getParameter('card_activation_delay'),30);
            if( ($interval->invert == 0) && ($diff != 0)){
                if($interval->m == $nbMonths){
                    if(($diff == 5) || ($diff == 2) || ($diff == 1)){
                        $subject = 'Activation de votre carte de sécurité Cairn';
                        $body = $this->renderView('CairnUserBundle:Emails:reminder_card_activation.html.twig',array('card'=>$card,'remainingDays'=>$diff));
                        $messageNotificator->notifyByEmail($subject,$from,$card->getUser()->getEmail(),$body);

                    }

                }
            }
            else{
                $subject = 'Expiration de votre carte de sécurité Cairn';
                $body = $this->renderView('CairnUserBundle:Emails:expiration_card.html.twig',array('card'=>$card,'diff'=>$diff));
                $card->getUser()->setCard(NULL);
                $saveEmail = $card->getUser()->getEmail();
                $em->remove($card);
                $em->flush();
                $messageNotificator->notifyByEmail($subject,$from,$saveEmail,$body);
            }
        }
        return new Response('ok');

    }
}

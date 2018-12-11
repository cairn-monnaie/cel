<?php
// src/Cairn/UserBundle/EventListener/ExceptionListener.php

namespace Cairn\UserBundle\EventListener;

use Cyclos;

use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\EventListener\SecurityListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserBundle\Service\AccessPlatform;
use Cairn\UserBundle\Service\Security;

use Doctrine\ORM\EntityManager;

use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * This class contains called functions when kernel.exception event is dispatched
 *
 */
class ExceptionListener
{

    protected $messageNotificator;
    protected $accessPlatform;
    protected $em;
    protected $router;
    protected $security;

    public function __construct(MessageNotificator $messageNotificator, AccessPlatform $accessPlatform, EntityManager $em, Router $router, Security $security)
    {
        $this->messageNotificator = $messageNotificator;
        $this->accessPlatform = $accessPlatform;
        $this->em = $em;
        $this->router = $router;
        $this->security = $security;
    }

    /**
     * Deals with the exception thrown 
     *
     * This function analyses the type of exception thrown and redispacth it accordingly. A cyclos exception will be notified to 
     * maintenance services or will simply redirect user to homepage depending. For instance, ENTITY_NOT_FOUND cyclos exceptions should
     * just redirect user, whereas ConnectionException must shutdown the platform automatically.
     *
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();
        $session = $request->getSession();

        $attributes = $request->attributes->all();                             
        $parameters = key_exists('_route_params', $attributes) ? $attributes['_route_params'] : array();

        $welcomeUrl = $this->router->generate('cairn_user_welcome');
        $logoutUrl = $this->router->generate('fos_user_security_logout');

        $currentRoute = ' Route courante : ' . $request->get('_route');
        $currentUrl = ' URL courante : ' . $currentUrl = $request->getRequestUri(); 
        $fromUrl = ' Depuis URL : ' .$request->headers->get('referer');

        $from = $this->messageNotificator->getNoReplyEmail();
        $to = $this->messageNotificator->getMaintenanceEmail();

        $exceptionMessage = 'Message d\'erreur : '. $exception->getMessage();

        $traceMessage = 'Trace : '. $exception->getTraceAsString();
        $fileMessage = 'Dans le fichier : ' . $exception->getFile();
        $lineMessage =  'A la ligne : ' .$exception->getLine();
        $routesMessage = $currentRoute . "\n" .$currentUrl ."\n". $fromUrl;

        $body = $routesMessage ."\n". $fileMessage ."\n". $lineMessage . "\n". $exceptionMessage . "\n" . $traceMessage;

        //this condition avoids circular exceptions thrown if the redirection page (login / homepage) contains an error
        if(strpos($request->headers->get('referer'), $request->getRequestUri()) && 
                                                     !$request->isMethod('POST') && 
                                                     !$this->security->isSensibleOperation($request->get('_route'),$parameters)
                                                 )
        {
            $subject = 'Erreur circulaire';
            $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
            $event->setResponse(new RedirectResponse($logoutUrl));
        }else{
            if($exception instanceof Cyclos\ServiceException){
                $subject = 'Erreur Cyclos';
                if($exception->errorCode == 'ENTITY_NOT_FOUND'){
                    $session->getFlashBag()->add('error','Donnée introuvable');
                    $event->setResponse(new RedirectResponse($welcomeUrl));

                }
                elseif($exception->errorCode == 'LOGIN'){
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Problème de connexion');
                    $event->setResponse(new RedirectResponse($logoutUrl));
                }
                elseif($exception->errorCode == 'PERMISSION_DENIED'){
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Vous n\'avez pas les droits nécessaires');
                    $event->setResponse(new RedirectResponse($welcomeUrl));
                }
                elseif($exception->errorCode == 'LOGGED_OUT'){
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Votre session a expiré. Veuillez vous reconnecter.');
                    $event->setResponse(new RedirectResponse($logoutUrl));
                }
                elseif($exception->errorCode == 'NULL_POINTER'){
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Donnée introuvable');
                    $event->setResponse(new RedirectResponse($welcomeUrl));
                }

                else{
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                    $event->setResponse(new RedirectResponse($welcomeUrl));
                }
            }
            elseif($exception instanceof Cyclos\ConnectionException){
                $subject = 'Maintenance automatique : ConnectionException Cyclos';

                //maintenance state : file written in web directory 
               file_put_contents("maintenance.txt", '');

                $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

                //will redirect to maintenance page
                $event->setResponse(new RedirectResponse($logoutUrl));
            }
//            else{//not cyclos error, instance of \Exception
//                $subject = 'Erreur technique Symfony';
            //    $codeMessage = 'Erreur code statut : ' .$exception->getCode();
            //    $body = $codeMessage. "\n" . $body;

//
//                if($exception instanceof ContextErrorException){
//                    $context = ' Contexte : ' . json_encode($exception->getContext());
//                    $body = $body . "\n". $context;
//                }
//                $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
//                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
//                $event->setResponse(new RedirectResponse($welcomeUrl));
//            }
        }


    }

}

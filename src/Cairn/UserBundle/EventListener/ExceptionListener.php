<?php
// src/Cairn/UserBundle/EventListener/ExceptionListener.php

namespace Cairn\UserBundle\EventListener;

use Cyclos;

use Symfony\Component\HttpFoundation\RedirectResponse; // N'oubliez pas ce use
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;

use Cairn\UserBundle\Event\SecurityEvents;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserBundle\Service\AccessPlatform;
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

    public function __construct(MessageNotificator $messageNotificator, AccessPlatform $accessPlatform, EntityManager $em, Router $router)
    {
        $this->messageNotificator = $messageNotificator;
        $this->accessPlatform = $accessPlatform;
        $this->em = $em;
        $this->router = $router;
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

        $previousUrl = $request->headers->get('referer');
        $welcomeUrl = $this->router->generate('cairn_user_welcome');
        $currentRoute = $request->get('_route');
        $currentUrl = $request->getPathInfo(); 

        $currentRoute = ' Route courante : ' . $currentRoute;
        $currentUrl = ' URL courante : ' . $currentUrl;
        $fromUrl = ' Depuis URL : ' .$previousUrl;

        $from = $this->messageNotificator->getNoReplyEmail();
        $to = $this->messageNotificator->getMaintenanceEmail();

        $exceptionMessage = $exception->getMessage();
        $routesMessage = $currentRoute . $currentUrl . $fromUrl;

        if($exception instanceof Cyclos\ServiceException){
            $subject = 'Erreur Cyclos';

            if($exception->errorCode == 'ENTITY_NOT_FOUND'){
                $session->getFlashBag()->add('error','Donnée introuvable');
            }
            else{
                //not all exceptions have this attribute
                if(property_exists($exception,'error')){
                    $error = ' Error : ' . json_encode($exception->error);
                }
                $body = $exceptionMessage . $error . $routesMessage;
                $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
            }
            $event->setResponse(new RedirectResponse($welcomeUrl));

        }
        elseif($exception instanceof Cyclos\ConnectionException){
            $subject = 'Erreur Cyclos';
            $body = $exceptionMessage . $routesMessage;

            //block all users
            $this->accessPlatform->shutDown(NULL);
            $this->em->flush();
            $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
            $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

            $event->setResponse(new RedirectResponse($welcomeUrl));

        }
//        else{//not cyclos error, instance of \Exception
//            $subject = 'Erreur technique';
//            $code = ' Code : ' . $exception->getCode();
//            $file = ' Fichier : ' . $exception->getFile();
//            $line = ' Ligne : ' . $exception->getLine();
//
//            $trace = ' Trace : ' .$exception->getTraceAsString();
//            $body = $exceptionMessage . $routesMessage . $code . $file .$line . $trace;
//            if($exception instanceof ContextErrorException){
//                $context = ' Contexte : ' . json_encode($exception->getContext());
//                $body = $body . $context;
//            }
//            $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
//            $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
//
//        }


    }

}

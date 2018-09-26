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

        $welcomeUrl = $this->router->generate('cairn_user_welcome');
        $logoutUrl = $this->router->generate('fos_user_security_logout');

        $currentRoute = ' Route courante : ' . $request->get('_route');
        $currentUrl = ' URL courante : ' . $currentUrl = $request->getRequestUri(); 
        $fromUrl = ' Depuis URL : ' .$request->headers->get('referer');

        $from = $this->messageNotificator->getNoReplyEmail();
        $to = $this->messageNotificator->getMaintenanceEmail();

        $exceptionMessage = 'Message d\'erreur : '. $exception->getMessage();

        $traceMessage = 'Trace : '. $exception->getTraceAsString();
        $lineMessage =  'A la ligne : ' .$exception->getLine();
        $routesMessage = $currentRoute . "\n" .$currentUrl ."\n". $fromUrl;

        $body = $routesMessage ."\n". $exceptionMessage . "\n" . $traceMessage;

        //this condition avoids circular exceptions thrown if the redirection page (login / homepage) contains an error
        if(strpos($request->headers->get('referer'), $request->getRequestUri())){
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
                else{
                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                    $event->setResponse(new RedirectResponse($welcomeUrl));
                }
            }
            elseif($exception instanceof Cyclos\ConnectionException){
                $subject = 'Maintenance automatique : ConnectionException Cyclos';

                //maintenance state 
                file_put_contents("../maintenance.txt", '');

                $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

                //will redirect to maintenance page
                $event->setResponse(new RedirectResponse($welcomeUrl));
            }
            else{//not cyclos error, instance of \Exception
                $subject = 'Erreur technique Symfony';

                if($exception instanceof ContextErrorException){
                    $context = ' Contexte : ' . json_encode($exception->getContext());
                    $body = $body . "\n". $context;
                }
                $session->getFlashBag()->add('error','Une erreur technique est survenue. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.');
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                $event->setResponse(new RedirectResponse($welcomeUrl));
            }
        }


    }

}

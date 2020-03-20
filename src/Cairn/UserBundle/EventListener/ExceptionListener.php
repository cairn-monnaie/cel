<?php
// src/Cairn/UserBundle/EventListener/ExceptionListener.php

namespace Cairn\UserBundle\EventListener;

use Cyclos;

use Symfony\Component\HttpFoundation\RedirectResponse; 
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Exception\HttpException;

use Cairn\UserBundle\Event\SecurityEvents;
use Cairn\UserBundle\EventListener\SecurityListener;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Cairn\UserBundle\Service\MessageNotificator;
use Cairn\UserBundle\Service\AccessPlatform;
use Cairn\UserBundle\Service\Security;
use Cairn\UserBundle\Service\Api;

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
    protected $api;

    public function __construct(MessageNotificator $messageNotificator, AccessPlatform $accessPlatform, EntityManager $em, Router $router, Security $security, Api $api)
    {
        $this->messageNotificator = $messageNotificator;
        $this->accessPlatform = $accessPlatform;
        $this->em = $em;
        $this->router = $router;
        $this->security = $security;
        $this->api = $api;

    }

    private function sendException(GetResponseForExceptionEvent $event, $errorMessage, $code, $redirectUrl=NULL)
    {
        if($this->api->isRemoteCall()){
            $code = ($code < 100) ? 500 : $code;
            $event->setResponse($this->api->getErrorResponse(array($errorMessage),$code));           
            return;
        }

        $session = $event->getRequest()->getSession();
        $session->getFlashBag()->add('error',$errorMessage);

        if($redirectUrl){
            $event->setResponse(new RedirectResponse($redirectUrl));
        }
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

        $exceptionMessage = 'Message d\'erreur : '. $exception->getMessage(). "\n";

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
            $event->setResponse(new RedirectResponse($logoutUrl));
        }else{
            if($exception instanceof Cyclos\ServiceException){
                $subject = 'Erreur Cyclos';
                if($exception->errorCode == 'ENTITY_NOT_FOUND'){
                    $errorMessage = 'Donnée introuvable';
                    $this->sendException($event, $errorMessage, Response::HTTP_NOT_FOUND,$welcomeUrl);
                }
                elseif($exception->errorCode == 'LOGIN'){
                    $errorMessage = 'Un problème technique est apparu pendant la phase de connexion. Notre service technique en a été automatiquement informé.';
                    $this->sendException($event, $errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR,$logoutUrl);
                }
                elseif($exception->errorCode == 'PERMISSION_DENIED'){
                    $errorMessage = 'Vous n\'avez pas les droits nécessaires';
                    $this->sendException($event, $errorMessage, Response::HTTP_UNAUTHORIZED, $welcomeUrl);
                }
                elseif($exception->errorCode == 'LOGGED_OUT'){//cyclos session token expired before Symfony

//                    $token = $loginManager->refreshSession();
//                    $session->set('cyclos_token',$this->security->vigenereEncode($token));
                    $session->getFlashBag()->add('info','Votre session a expiré. Veuillez vous reconnecter.');
                    $event->setResponse(new RedirectResponse($logoutUrl));
                }
                elseif($exception->errorCode == 'NULL_POINTER'){
                    $errorMessage = 'Donnée introuvable';
                    $this->sendException($event, $errorMessage, Response::HTTP_NOT_FOUND,$welcomeUrl);
                }
                elseif($exception->errorCode == 'VALIDATION'){
                    $listErrors = '';
                    for($i = 0; $i < count($exception->error->validation->allErrors); $i++){
                        $listErrors = "\n".$exception->error->validation->allErrors[$i].$listErrors;
                    }
                    $body = $listErrors . $body;

                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $errorMessage = 'Un problème technique est survenu pendant votre opération. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.';
                    $this->sendException($event, $errorMessage, Response::HTTP_BAD_REQUEST, $welcomeUrl);
                }
                else{
                    $errorMessage = 'Un problème technique est survenu. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.';
                    $this->sendException($event, $errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR,$welcomeUrl);
                }
            }
            elseif($exception instanceof Cyclos\ConnectionException){
                $subject = 'Maintenance automatique : ConnectionException Cyclos';

                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

                //maintenance state : file written in web directory 
               file_put_contents("maintenance.txt", '');

               $errorMessage = 'Un problème technique est survenu. Notre service technique en a été informé et traitera le problème dans les plus brefs délais.';
               $this->sendException($event, $errorMessage,Response::HTTP_INTERNAL_SERVER_ERROR, $logoutUrl);
            }else{
                if ($exception instanceof HttpException) {
                    $code =  $exception->getStatusCode();
                } elseif ($exception->getCode() >= Response::HTTP_BAD_REQUEST){
                    $code = $exception->getCode();
                }else{
                    $code = Response::HTTP_INTERNAL_SERVER_ERROR;
                }
     
                $this->sendException($event, $exception->getMessage(),$code);
            }
        }

    }

}

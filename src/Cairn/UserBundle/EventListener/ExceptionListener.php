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

    private function sendException(GetResponseForExceptionEvent $event, $errorMessage, $code, $redirectKey=NULL)
    {
        if($this->api->isRemoteCall() || $redirectKey){
            $code = ($code < 100) ? 500 : $code;
            $errors = [];
            $errors[] = ['key'=>$errorMessage,'args'=>[]];

            $event->setResponse($this->api->getErrorsResponse($errors,[],$code,$redirectKey));
            return;
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
            $this->sendException($event, $subject, Response::HTTP_BAD_REQUEST,'fos_user_security_logout');
        }else{
            if($exception instanceof Cyclos\ServiceException){
                $subject = 'Erreur Cyclos';
                if($exception->errorCode == 'ENTITY_NOT_FOUND'){
                    $errorMessage = 'cyclos_data_not_found';
                    $this->sendException($event, $errorMessage, Response::HTTP_NOT_FOUND,'cairn_user_welcome');
                }
                elseif($exception->errorCode == 'LOGIN'){
                    $errorMessage = 'internal_server_error';
                    $this->sendException($event, $errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR,'fos_user_security_logout');
                }
                elseif($exception->errorCode == 'PERMISSION_DENIED'){
                    $errorMessage = 'cyclos_permission_denied';
                    $this->sendException($event, $errorMessage, Response::HTTP_FORBIDDEN, 'cairn_user_welcome');
                }
                elseif($exception->errorCode == 'LOGGED_OUT'){//cyclos session token expired before Symfony
                    $errorMessage = 'session_expired';
                    $this->sendException($event, $errorMessage, Response::HTTP_FORBIDDEN, 'fos_user_security_logout');
                }
                elseif($exception->errorCode == 'NULL_POINTER'){
                    $errorMessage = 'cyclos_data_not_found';
                    $this->sendException($event, $errorMessage, Response::HTTP_NOT_FOUND, 'cairn_user_welcome');
                }
                elseif($exception->errorCode == 'VALIDATION'){
                    $listErrors = '';
                    for($i = 0; $i < count($exception->error->validation->allErrors); $i++){
                        $listErrors = "\n".$exception->error->validation->allErrors[$i].$listErrors;
                    }
                    $body = $listErrors . $body;

                    $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);
                    $errorMessage = 'cyclos_validation_error';
                    $this->sendException($event, $errorMessage, Response::HTTP_BAD_REQUEST, 'cairn_user_welcome');
                }
                else{
                    $errorMessage = 'internal_server_error';
                    $this->sendException($event, $errorMessage, Response::HTTP_INTERNAL_SERVER_ERROR,'cairn_user_welcome');
                }
            }
            elseif($exception instanceof Cyclos\ConnectionException){
                $subject = 'Maintenance automatique : ConnectionException Cyclos';
                $this->messageNotificator->notifyByEmail($subject,$from,$to,$body);

                //maintenance state : file written in web directory 
               file_put_contents("maintenance.txt", '');

               $errorMessage = 'cyclos_connection_error';
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

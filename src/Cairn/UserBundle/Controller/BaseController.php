<?php
// src/Cairn/UserBundle/Controller/BaseController.php

namespace Cairn\UserBundle\Controller;

//manage Cyclos configuration file
use Cyclos;

//manage Controllers & Entities
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

//manage HTTP format
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\HttpFoundation\StreamedResponse;

//manage Forms
use Symfony\Component\Form\FormInterface;

use Cairn\UserBundle\Service\Messages;

class BaseController extends Controller
{ 

    protected function getFormResponse(string $renderPath,array $renderParams = [], FormInterface $form,$messages = [])
    {
        $apiService = $this->get('cairn_user.api');
        return $apiService->getFormResponse($renderPath,$renderParams,$form,$messages);
    }

    protected function getRenderResponse(string $renderPath,array $renderParams = [], $data, $statusCode, $messages = [])
    {
        $apiService = $this->get('cairn_user.api');
        return $apiService->getRenderResponse($renderPath,$renderParams,$data,$statusCode,$messages);
    }

    protected function getRedirectionResponse(string $redirectKey,array $redirectParams = [], $data, $statusCode, $messages = [])
    {
        $apiService = $this->get('cairn_user.api');
        return $apiService->getRedirectionResponse($redirectKey,$redirectParams,$data,$statusCode,$messages);
    }

    protected function getErrorsResponse($errors, $messages, $statusCode,$redirectKey='cairn_user_welcome')
    {
        $apiService = $this->get('cairn_user.api');
        return $apiService->getErrorsResponse($errors, $messages, $statusCode,$redirectKey);
    }
}


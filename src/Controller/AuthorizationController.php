<?php

namespace App\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AuthorizationController extends AbstractController
{
    public function __construct(
        private readonly AuthorizationServer $server,
    ) {
    }

    #[Route('/authorize', name: 'oauth_authorize', methods: ['GET'])]
    public function authorize(Request $request): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $httpFoundationFactory = new HttpFoundationFactory();

        $psrRequest = $psrHttpFactory->createRequest($request);
        $psrResponse = $psr17Factory->createResponse();

        try {
            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);
            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $authRequest->setUser($user);
            $authRequest->setAuthorizationApproved(true);
            $psrResponse = $this->server->completeAuthorizationRequest($authRequest, $psrResponse);
        } catch (OAuthServerException $e) {
            $psrResponse = $e->generateHttpResponse($psrResponse);
        }

        return $httpFoundationFactory->createResponse($psrResponse);
    }
}

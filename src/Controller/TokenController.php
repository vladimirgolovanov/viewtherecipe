<?php

declare(strict_types=1);

namespace App\Controller;

use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Exception\OAuthServerException;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Log\LoggerInterface;
use Symfony\Bridge\PsrHttpMessage\Factory\HttpFoundationFactory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TokenController extends AbstractController
{
    public function __construct(
        private AuthorizationServer $server,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/token', name: 'oauth2_token', methods: ['POST'])]
    public function token(Request $request): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrFactory->createRequest($request);
        $psrResponse = $psr17Factory->createResponse();

        try {
            $psrResponse = $this->server->respondToAccessTokenRequest($psrRequest, $psrResponse);
        } catch (OAuthServerException $e) {
            $this->logger->error('OAuth2 token error', [
                'message' => $e->getMessage(),
                'hint' => $e->getHint(),
                'error_type' => $e->getErrorType(),
            ]);
            $psrResponse = $e->generateHttpResponse($psrResponse);
        }

        $httpFoundationFactory = new HttpFoundationFactory();

        return $httpFoundationFactory->createResponse($psrResponse);
    }
}

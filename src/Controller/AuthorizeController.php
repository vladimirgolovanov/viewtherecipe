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
use Symfony\Component\Security\Http\Attribute\IsGranted;

class AuthorizeController extends AbstractController
{
    public function __construct(
        private AuthorizationServer $server,
        private LoggerInterface $logger,
    ) {
    }

    #[Route('/dev/login', name: 'dev_login', methods: ['GET'])]
    public function devLogin(Request $request, \App\Repository\UserRepository $userRepository): Response
    {
        if ($this->getParameter('kernel.environment') !== 'dev') {
            throw $this->createNotFoundException();
        }

        $user = $userRepository->find(1);
        $token = new \Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken(
            $user,
            'oauth_authorize',
            $user->getRoles()
        );
        $this->container->get('security.token_storage')->setToken($token);
        $request->getSession()->set('_security_main', serialize($token));
        $request->getSession()->save();

        return new Response('logged in as '.$user->getUserIdentifier());
    }

    #[IsGranted('ROLE_USER')]
    #[Route('/authorize', name: 'oauth2_authorize', methods: ['GET'])]
    public function authorize(Request $request): Response
    {
        $psr17Factory = new Psr17Factory();
        $psrFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrFactory->createRequest($request);
        $psrResponse = $psr17Factory->createResponse();

        try {
            $this->logger->debug('OAuth2 authorize request', [
                'query' => $request->query->all(),
                'headers' => $request->headers->all(),
            ]);

            $this->logger->debug('PSR request', [
                'query' => $psrRequest->getQueryParams(),
                'server' => $psrRequest->getServerParams(),
            ]);

            /** @var \App\Entity\User $user */
            $user = $this->getUser();
            $params = $psrRequest->getQueryParams();
            if (!isset($params['client_id'])) {
                $psrRequest = $psrRequest->withQueryParams(['client_id' => $user->getUserIdentifier()] + $params);
            }

            $authRequest = $this->server->validateAuthorizationRequest($psrRequest);
            $authRequest->setUser($user);
            $authRequest->setAuthorizationApproved(true);
            $psrResponse = $this->server->completeAuthorizationRequest($authRequest, $psrResponse);
        } catch (OAuthServerException $e) {
            $this->logger->error('OAuth2 authorize error', [
                'message' => $e->getMessage(),
                'hint' => $e->getHint(),
                'error_type' => $e->getErrorType(),
            ]);
            $psrResponse = $e->generateHttpResponse($psrResponse);
        }

        return new HttpFoundationFactory()->createResponse($psrResponse);
    }
}

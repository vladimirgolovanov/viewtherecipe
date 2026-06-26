<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WellKnownController extends AbstractController
{
    #[Route('/.well-known/oauth-authorization-server', name: 'oauth_well_known', methods: ['GET'])]
    public function authorizationServer(): JsonResponse
    {
        $baseUrl = $this->generateUrl('oauth_well_known', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $issuer = rtrim(str_replace('/.well-known/oauth-authorization-server', '', $baseUrl), '/');

        return $this->json([
            'issuer' => $issuer,
            'authorization_endpoint' => $this->generateUrl('oauth_authorize', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'token_endpoint' => $this->generateUrl('oauth_token', [], UrlGeneratorInterface::ABSOLUTE_URL),
            'response_types_supported' => ['code'],
            'grant_types_supported' => ['authorization_code', 'refresh_token'],
            'code_challenge_methods_supported' => ['S256'],
            'token_endpoint_auth_methods_supported' => ['none'],
            'scopes_supported' => ['email'],
        ]);
    }

    #[Route('/.well-known/oauth-protected-resource', name: 'oauth_well_known_protected_resource', methods: ['GET'])]
    public function oauthProtectedResource(): JsonResponse
    {
        $baseUrl = $this->generateUrl('oauth_well_known', [], UrlGeneratorInterface::ABSOLUTE_URL);
        $issuer = rtrim(str_replace('/.well-known/oauth-authorization-server', '', $baseUrl), '/');

        return $this->json([
            'resource' => $issuer.'/mcp',
            'authorization_servers' => [$issuer],
            'registration_endpoint' => $issuer.'/register',
        ]);
    }
}

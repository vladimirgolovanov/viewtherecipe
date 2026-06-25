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
        return $this->json([
            'resource' => 'http://localhost:8000/mcp',
            'authorization_servers' => ['http://localhost:8000'],
            'registration_endpoint' => 'http://localhost:8000/register',
        ]);
    }
}

/*
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class WellKnownController extends AbstractController
{
    #[Route('/.well-known/oauth-protected-resource', name: 'oauth2_well_known', methods: ['GET'])]
    public function protectedResource(Request $request): JsonResponse
    {
        $baseUrl = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'resource' => $baseUrl.'/mcp',
            'authorization_servers' => [$baseUrl],
            'bearer_methods_supported' => ['header'],
            'scopes_supported' => ['mcp'],
        ]);
    }

    #[Route('/.well-known/oauth-protected-resource/mcp', name: 'oauth2_well_known_mcp', methods: ['GET'])]
    public function protectedResourceMcp(Request $request): JsonResponse
    {
        $baseUrl = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'resource' => $baseUrl.'/mcp',
            'authorization_servers' => [$baseUrl],
            'bearer_methods_supported' => ['header'],
            'scopes_supported' => ['mcp'],
        ]);
    }

    #[Route('/.well-known/oauth-authorization-server', name: 'oauth2_authorization_server', methods: ['GET'])]
    public function authorizationServer(Request $request): JsonResponse
    {
        $baseUrl = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'issuer' => $baseUrl,
            'authorization_endpoint' => $baseUrl.'/authorize',
            'token_endpoint' => $baseUrl.'/token',
            'response_types_supported' => [
                'code',
            ],
            'grant_types_supported' => [
                'authorization_code',
                'refresh_token',
            ],
            'scopes_supported' => [
                'mcp',
            ],
            'token_endpoint_auth_methods_supported' => [
                'none',
            ],
            'code_challenge_methods_supported' => [
                'S256',
            ],
            'client_id_metadata_document_supported' => false,
            'token_endpoint_auth_signing_alg_values_supported' => [],
            'authorization_response_iss_parameter_supported' => true,
        ]);
    }
}*/

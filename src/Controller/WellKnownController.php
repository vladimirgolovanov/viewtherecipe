<?php

declare(strict_types=1);

namespace App\Controller;

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

    #[Route('/.well-known/oauth-authorization-server', name: 'oauth2_authorization_server', methods: ['GET'])]
    public function authorizationServer(Request $request): JsonResponse
    {
        $baseUrl = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'issuer' => $baseUrl,
            'authorization_endpoint' => $baseUrl.'/authorize',
            'token_endpoint' => $baseUrl.'/token',
            'grant_types_supported' => ['authorization_code'],
            'response_types_supported' => ['code'],
            'token_endpoint_auth_methods_supported' => ['none'],
            'scopes_supported' => ['mcp'],
        ]);
    }

    #[Route('/.well-known/openid-configuration', name: 'oauth2_openid_configuration', methods: ['GET'])]
    public function openidConfiguration(Request $request): JsonResponse
    {
        $baseUrl = $request->getSchemeAndHttpHost();

        return new JsonResponse([
            'issuer' => $baseUrl,
            'authorization_endpoint' => $baseUrl.'/authorize',
            'token_endpoint' => $baseUrl.'/token',
            'grant_types_supported' => ['authorization_code'],
            'response_types_supported' => ['code'],
            'token_endpoint_auth_methods_supported' => ['none'],
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
}

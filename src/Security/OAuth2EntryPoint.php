<?php

declare(strict_types=1);

namespace App\Security;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface;

class OAuth2EntryPoint implements AuthenticationEntryPointInterface
{
    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $resourceMetadata = $request->getSchemeAndHttpHost().'/.well-known/oauth-protected-resource';

        return new JsonResponse(
            ['error' => 'access_denied', 'error_description' => 'Authentication required'],
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 'Bearer realm="MCP", resource_metadata="'.$resourceMetadata.'"'],
        );
    }
}

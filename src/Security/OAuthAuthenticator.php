<?php

namespace App\Security;

use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use Nyholm\Psr7\Factory\Psr17Factory;
use Symfony\Bridge\PsrHttpMessage\Factory\PsrHttpFactory;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;
use Symfony\Component\Security\Http\EntryPoint\AuthenticationEntryPointInterface; // ← добавить

class OAuthAuthenticator extends AbstractAuthenticator implements AuthenticationEntryPointInterface // ← добавить
{
    public function __construct(
        private readonly ResourceServer $resourceServer,
    ) {
    }

    private function buildWwwAuthenticate(Request $request, ?string $error = null, ?string $description = null): string
    {
        $metadataUrl = $request->getSchemeAndHttpHost().'/.well-known/oauth-protected-resource';

        $parts = [
            'Bearer realm="MCP"',
            sprintf('resource_metadata="%s"', $metadataUrl),
        ];

        if ($error !== null) {
            $parts[] = sprintf('error="%s"', $error);
        }

        if ($description !== null) {
            $clean = str_replace(['"', "\n", "\r"], ["'", ' ', ''], $description);
            $parts[] = sprintf('error_description="%s"', $clean);
        }

        return implode(', ', $parts);
    }

    public function supports(Request $request): ?bool
    {
        return $request->headers->has('Authorization')
            && str_starts_with($request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $psr17Factory = new Psr17Factory();
        $psrHttpFactory = new PsrHttpFactory($psr17Factory, $psr17Factory, $psr17Factory, $psr17Factory);
        $psrRequest = $psrHttpFactory->createRequest($request);

        try {
            $psrRequest = $this->resourceServer->validateAuthenticatedRequest($psrRequest);
        } catch (OAuthServerException $e) {
            throw new AuthenticationException($e->getMessage(), 0, $e);
        }

        $userId = $psrRequest->getAttribute('oauth_user_id');

        return new SelfValidatingPassport(
            new UserBadge($userId)
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        $response = new JsonResponse([
            'error' => 'invalid_token',
            'error_description' => $exception->getMessage(),
        ], Response::HTTP_UNAUTHORIZED);

        $response->headers->set(
            'WWW-Authenticate',
            $this->buildWwwAuthenticate($request, 'invalid_token', $exception->getMessage())
        );

        return $response;
    }

    public function start(Request $request, ?AuthenticationException $authException = null): Response
    {
        $response = new JsonResponse([
            'error' => 'unauthorized',
            'error_description' => 'Authentication required',
        ], Response::HTTP_UNAUTHORIZED);

        $response->headers->set('WWW-Authenticate', $this->buildWwwAuthenticate($request));

        return $response;
    }
}

<?php

declare(strict_types=1);

namespace App\Security;

use App\Entity\OAuth2AccessToken;
use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Http\Authenticator\AbstractAuthenticator;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class OAuth2TokenAuthenticator extends AbstractAuthenticator
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserRepository $userRepository,
    ) {
    }

    public function supports(Request $request): ?bool
    {
        return str_starts_with($request->getPathInfo(), '/mcp')
            && $request->headers->has('Authorization')
            && str_starts_with((string) $request->headers->get('Authorization'), 'Bearer ');
    }

    public function authenticate(Request $request): Passport
    {
        $tokenId = substr((string) $request->headers->get('Authorization'), 7);

        return new SelfValidatingPassport(
            new UserBadge($tokenId, function (string $tokenId): User {
                $token = $this->em->getRepository(OAuth2AccessToken::class)->find($tokenId);

                if (!$token || $token->isRevoked() || $token->getExpiryDateTime() < new \DateTimeImmutable()) {
                    throw new AuthenticationException('Invalid or expired OAuth2 token.');
                }

                $user = $this->userRepository->loadUserByIdentifier($token->getUserIdentifier() ?? '');
                if (!$user instanceof User) {
                    throw new AuthenticationException('User not found.');
                }

                return $user;
            })
        );
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token, string $firewallName): ?Response
    {
        $tokenId = substr((string) $request->headers->get('Authorization'), 7);
        $accessToken = $this->em->getRepository(OAuth2AccessToken::class)->find($tokenId);
        if ($accessToken !== null) {
            $request->attributes->set('oauth2_client_id', $accessToken->getClient()->getIdentifier());
        }

        return null;
    }

    public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
    {
        return new JsonResponse(
            ['error' => 'access_denied', 'error_description' => $exception->getMessage()],
            Response::HTTP_UNAUTHORIZED,
            ['WWW-Authenticate' => 'Bearer realm="MCP", resource_metadata="/.well-known/oauth-protected-resource"'],
        );
    }
}

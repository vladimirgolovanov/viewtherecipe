<?php

declare(strict_types=1);

namespace App\EventSubscriber;

use App\Entity\OAuth2AccessToken;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class OAuth2Subscriber implements EventSubscriberInterface
{
    private const PUBLIC_ROUTES = [
        'oauth2_token',
        'oauth2_well_known',
    ];

    public function __construct(
        private EntityManagerInterface $em
    ) {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::REQUEST => ['onRequest', 10],
        ];
    }

    public function onRequest(RequestEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();
        $route = $request->attributes->get('_route');

        if (in_array($route, self::PUBLIC_ROUTES, true)) {
            return;
        }

        if (!str_starts_with($request->getPathInfo(), '/mcp')) {
            return;
        }

        $header = $request->headers->get('Authorization', '');
        $tokenId = str_replace('Bearer ', '', $header);

        if (!$tokenId) {
            $this->deny($event, 'Missing Authorization header');

            return;
        }

        $token = $this->em->getRepository(OAuth2AccessToken::class)->find($tokenId);

        if (!$token || $token->isRevoked() || $token->getExpiryDateTime() < new \DateTimeImmutable()) {
            $this->deny($event, 'Invalid or expired token');

            return;
        }

        $request->attributes->set('oauth2_client_id', $token->getClient()->getIdentifier());
    }

    private function deny(RequestEvent $event, string $message): void
    {
        $event->setResponse(new JsonResponse(
            ['error' => 'access_denied', 'error_description' => $message],
            401,
            ['WWW-Authenticate' => 'Bearer realm="MCP", resource_metadata="/.well-known/oauth-protected-resource"'],
        ));
    }
}

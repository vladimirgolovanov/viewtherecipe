<?php

namespace App\EventListener;

use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

final class RequestLoggerListener
{
    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly bool $enabled = true,
    ) {
    }

    #[AsEventListener(priority: 300)]
    public function onRequestEvent(RequestEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }

        $request = $event->getRequest();

        $this->logger->info('Incoming request', [
            'method' => $request->getMethod(),
            'uri' => $request->getUri(),
            'query' => $request->query->all(),
            'body' => $request->getContent(),
            'headers' => $request->headers->all(),
            'client_ip' => $request->getClientIp(),
        ]);
    }

    #[AsEventListener]
    public function onResponseEvent(ResponseEvent $event): void
    {
        if (!$this->enabled || !$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();

        $this->logger->info('Outgoing response', [
            'status' => $response->getStatusCode(),
            'headers' => $response->headers->all(),
            'body' => $response->getContent(),
        ]);
    }
}

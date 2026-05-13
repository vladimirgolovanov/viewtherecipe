<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class TelegramAuthController extends AbstractController
{
    #[Route('/api/auth/telegram', name: 'app_telegram_auth', methods: ['GET'])]
    public function __invoke(): Response
    {
        // TelegramAuthenticator handles this request and returns the response.
        // This body is never reached on success or failure.
        return new Response(null, Response::HTTP_UNAUTHORIZED);
    }
}

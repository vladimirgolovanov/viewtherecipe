<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class LogoutController
{
    #[Route('/api/auth/logout', name: 'app_api_logout', methods: ['POST'])]
    public function __invoke(Request $request): JsonResponse
    {
        $request->getSession()->invalidate();

        return new JsonResponse(['success' => true]);
    }
}

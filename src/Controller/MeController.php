<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MeController extends AbstractController
{
    public function __construct(private Security $security)
    {
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $token = $user->getApiToken();

        return $this->json([
            'api_token' => $token,
            'mcp_url' => 'https://savetherecipe.golovanov.me/mcp',
            'mcp_config' => [
                'mcpServers' => [
                    'savetherecipe' => [
                        'type' => 'http',
                        'url' => 'https://savetherecipe.golovanov.me/mcp',
                        'headers' => [
                            'Authorization' => 'Bearer '.$token,
                        ],
                    ],
                ],
            ],
        ]);
    }
}

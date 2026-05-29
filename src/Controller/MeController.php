<?php

namespace App\Controller;

use App\Entity\OAuth2AccessToken;
use App\Entity\OAuth2Client;
use App\Entity\User;
use App\Repository\AccessTokenRepository;
use App\Repository\OAuth2ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

class MeController extends AbstractController
{
    public function __construct(
        private Security $security,
        private OAuth2ClientRepository $clientRepository,
        private AccessTokenRepository $tokenRepository,
        private EntityManagerInterface $em,
    ) {
    }

    #[Route('/api/me', name: 'api_me', methods: ['GET'])]
    public function __invoke(): JsonResponse
    {
        /** @var User $user */
        $user = $this->security->getUser();

        $client = $this->clientRepository->findByUser($user);
        if ($client === null) {
            $client = new OAuth2Client();
            $client->setUser($user);
            $this->em->persist($client);
            $this->em->flush();
        }

        $token = $this->tokenRepository->findValidByClient($client);
        if ($token === null) {
            $token = new OAuth2AccessToken();
            $token->setClient($client);
            $this->em->persist($token);
            $this->em->flush();
        }

        return $this->json([
            'api_token' => $user->getApiToken(),
            'mcp_url' => 'https://savetherecipe.golovanov.me/mcp',
            'mcp_config' => [
                'mcpServers' => [
                    'savetherecipe' => [
                        'type' => 'http',
                        'url' => 'https://savetherecipe.golovanov.me/mcp',
                        'headers' => [
                            'Authorization' => 'Bearer '.$token->getIdentifier(),
                        ],
                    ],
                ],
            ],
        ]);
    }
}

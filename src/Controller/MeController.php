<?php

namespace App\Controller;

use App\Entity\OAuth2Client;
use App\Entity\User;
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

        return $this->json([
            'client_id' => $client->getIdentifier(),
            'client_secret' => $client->getSecret(),
        ]);
    }
}

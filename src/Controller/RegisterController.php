<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterController extends AbstractController
{
    #[Route('/register', name: 'app_register', methods: ['POST'])]
    public function index(Request $request, ClientRepository $clientRepository, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $client = new Client();
        $client->setClientId(bin2hex(random_bytes(16)));
        $client->setRedirectUris($data['redirect_uris'] ?? []);
        $client->setScopes([]);
        $client->setConfidential(false);

        $em->persist($client);
        $em->flush();

        return new JsonResponse([
            'client_id' => $client->getClientId(),
            'client_id_issued_at' => time(),
        ], 201);
    }
}

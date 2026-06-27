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

        $clientId = $data['client_id'] ?? null;

        if (!$clientId) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'client_id is required'], 400);
        }

        if (!filter_var($clientId, FILTER_VALIDATE_URL)) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'client_id must be a URL'], 400);
        }

        $response = file_get_contents($clientId);
        if ($response === false) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'could not fetch client metadata'], 400);
        }

        $metadata = json_decode($response, true);
        if (!$metadata) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'invalid JSON at client_id URL'], 400);
        }

        if (($metadata['client_id'] ?? null) !== $clientId) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'client_id mismatch'], 400);
        }

        $requestedUris = $data['redirect_uris'] ?? [];
        $metadataUris = $metadata['redirect_uris'] ?? [];
        foreach ($requestedUris as $uri) {
            if (!in_array($uri, $metadataUris)) {
                return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'redirect_uri not in metadata'], 400);
            }
        }

        $existing = $clientRepository->findOneBy(['clientId' => $clientId]);
        if ($existing) {
            return new JsonResponse([
                'client_id' => $existing->getClientId(),
                'client_id_issued_at' => time(),
            ]);
        }

        $client = new Client();
        $clientId = $data['client_id'] ?? bin2hex(random_bytes(16));
        $client->setClientId($clientId);
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

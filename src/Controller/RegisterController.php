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

        $redirectUris = $data['redirect_uris'] ?? [];
        if (empty($redirectUris)) {
            return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'redirect_uris is required'], 400);
        }

        $clientId = $data['client_id'] ?? null;

        if ($clientId) {
            if (!filter_var($clientId, FILTER_VALIDATE_URL)) {
                return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'client_id must be a URL'], 400);
            }

            $response = @file_get_contents($clientId);
            if ($response === false) {
                return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'could not fetch client metadata'], 400);
            }

            $metadata = json_decode($response, true);
            if (!$metadata || ($metadata['client_id'] ?? null) !== $clientId) {
                return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'client_id mismatch'], 400);
            }

            foreach ($redirectUris as $uri) {
                if (!in_array($uri, $metadata['redirect_uris'] ?? [])) {
                    return new JsonResponse(['error' => 'invalid_client_metadata', 'error_description' => 'redirect_uri not in metadata'], 400);
                }
            }
        } else {
            $clientId = bin2hex(random_bytes(16));
        }

        $existing = $clientRepository->findOneBy(['clientId' => $clientId]);
        if ($existing) {
            return new JsonResponse([
                'client_id' => $existing->getClientId(),
                'client_id_issued_at' => time(),
            ]);
        }

        $client = new Client();
        $client->setClientId($clientId);
        $client->setRedirectUris($redirectUris);
        $client->setScopes([]);
        $client->setConfidential(false);

        $em->persist($client);
        $em->flush();

        return new JsonResponse([
            'client_id' => $client->getClientId(),
            'client_id_issued_at' => time(),
            'redirect_uris' => $redirectUris,
            'grant_types' => $data['grant_types'] ?? ['authorization_code'],
            'response_types' => $data['response_types'] ?? ['code'],
            'token_endpoint_auth_method' => $data['token_endpoint_auth_method'] ?? 'none',
            'scope' => $data['scope'] ?? '',
            'client_name' => $data['client_name'] ?? '',
            'application_type' => $data['application_type'] ?? 'web',
        ], 201);
    }
}

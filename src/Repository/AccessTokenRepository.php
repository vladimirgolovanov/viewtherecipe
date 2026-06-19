<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OAuth2AccessToken;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, ?string $userIdentifier = null): AccessTokenEntityInterface
    {
        $token = new OAuth2AccessToken();
        $token->setClient($clientEntity);
        if ($userIdentifier !== null) {
            $token->setUserIdentifier($userIdentifier);
        }
        foreach ($scopes as $scope) {
            $token->addScope($scope);
        }

        return $token;
    }

    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $this->em->persist($accessTokenEntity);
        $this->em->flush();
    }

    public function revokeAccessToken(string $tokenId): void
    {
        $token = $this->em->getRepository(OAuth2AccessToken::class)->find($tokenId);
        $token?->revoke();
        $this->em->flush();
    }

    public function isAccessTokenRevoked(string $tokenId): bool
    {
        $token = $this->em->getRepository(OAuth2AccessToken::class)->find($tokenId);

        return null === $token || $token->isRevoked();
    }
}

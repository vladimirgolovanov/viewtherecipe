<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OAuth2RefreshToken;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getNewRefreshToken(): ?RefreshTokenEntityInterface
    {
        return new OAuth2RefreshToken();
    }

    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $this->em->persist($refreshTokenEntity);
        $this->em->flush();
    }

    public function revokeRefreshToken(string $tokenId): void
    {
        $token = $this->em->getRepository(OAuth2RefreshToken::class)->find($tokenId);
        $token?->revoke();
        $this->em->flush();
    }

    public function isRefreshTokenRevoked(string $tokenId): bool
    {
        $token = $this->em->getRepository(OAuth2RefreshToken::class)->find($tokenId);

        return null === $token || $token->isRevoked();
    }
}

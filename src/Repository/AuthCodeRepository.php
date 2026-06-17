<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\OAuth2AuthCode;
use Doctrine\ORM\EntityManagerInterface;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    public function __construct(private EntityManagerInterface $em)
    {
    }

    public function getNewAuthCode(): AuthCodeEntityInterface
    {
        return new OAuth2AuthCode();
    }

    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $this->em->persist($authCodeEntity);
        $this->em->flush();
    }

    public function revokeAuthCode(string $codeId): void
    {
        $code = $this->em->getRepository(OAuth2AuthCode::class)->find($codeId);
        $code?->revoke();
        $this->em->flush();
    }

    public function isAuthCodeRevoked(string $codeId): bool
    {
        $code = $this->em->getRepository(OAuth2AuthCode::class)->find($codeId);

        return null === $code || $code->isRevoked();
    }
}


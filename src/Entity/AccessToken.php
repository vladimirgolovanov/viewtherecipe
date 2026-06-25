<?php

namespace App\Entity;

use App\Repository\AccessTokenRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;

#[ORM\Entity(repositoryClass: AccessTokenRepository::class)]
class AccessToken implements AccessTokenEntityInterface
{
    use EntityTrait;
    use AccessTokenTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    protected string $identifier;

    #[ORM\ManyToOne(targetEntity: Client::class)]
    #[ORM\JoinColumn(nullable: false)]
    protected ClientEntityInterface $client;

    #[ORM\Column(length: 255, nullable: true)]
    protected ?string $userIdentifier = null;

    #[ORM\Column]
    protected \DateTimeImmutable $expiryDateTime;

    #[ORM\Column]
    private ?bool $revoked = null;

    protected array $scopes = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    public function setClient(Client|ClientEntityInterface|null $client): void
    {
        $this->client = $client;
    }

    public function getUserIdentifier(): ?string
    {
        return $this->userIdentifier;
    }

    public function setUserIdentifier(?string $identifier): void
    {
        $this->userIdentifier = $identifier;
    }

    public function isRevoked(): ?bool
    {
        return $this->revoked;
    }

    public function setRevoked(bool $revoked): static
    {
        $this->revoked = $revoked;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function addScope(ScopeEntityInterface $scope): void
    {
        $this->scopes[] = $scope->getIdentifier();
    }

    public function getExpiryDateTime(): \DateTimeImmutable
    {
        return $this->expiryDateTime;
    }

    public function setExpiryDateTime(\DateTimeImmutable $dateTime): void
    {
        $this->expiryDateTime = $dateTime;
    }
}

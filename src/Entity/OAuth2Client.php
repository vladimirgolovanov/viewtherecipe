<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;

#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(),
        new Delete(),
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'oauth2_client')]
class OAuth2Client implements ClientEntityInterface
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 80)]
    private string $identifier;

    #[ORM\Column(type: 'string', length: 255)]
    private string $secret;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $name = null;

    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function __construct()
    {
        $this->identifier = bin2hex(random_bytes(8));
        $this->secret = bin2hex(random_bytes(16));
    }

    public function getIdentifier(): string
    {
        return $this->identifier;
    }

    public function getName(): string
    {
        return $this->name ?? $this->identifier;
    }

    public function getRedirectUri(): array|string
    {
        return [];
    }

    public function isConfidential(): bool
    {
        return true;
    }

    public function validateSecret(string $secret): bool
    {
        return hash_equals($this->secret, $secret);
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(User $user): void
    {
        $this->user = $user;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }
}

<?php

namespace App\Entity;

use App\Repository\ClientRepository;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\ClientTrait;

#[ORM\Entity(repositoryClass: ClientRepository::class)]
class Client implements ClientEntityInterface
{
    use ClientTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 80)]
    private ?string $clientId = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $secret = null;

    #[ORM\Column]
    private array $redirectUris = [];

    #[ORM\Column]
    private array $scopes = [];

    #[ORM\Column(type: 'boolean')]
    protected bool $isConfidential = false;

    public function getIdentifier(): string
    {
        return $this->clientId;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function setClientId(string $clientId): static
    {
        $this->clientId = $clientId;

        return $this;
    }

    public function getName(): string
    {
        return $this->clientId;
    }

    public function getSecret(): ?string
    {
        return $this->secret;
    }

    public function setSecret(?string $secret): static
    {
        $this->secret = $secret;

        return $this;
    }

    public function getRedirectUri(): string|array
    {
        $uris = $this->redirectUris;
        return count($uris) === 1 ? $uris[0] : $uris;
    }

    public function setRedirectUris(array $redirectUris): static
    {
        $this->redirectUris = $redirectUris;

        return $this;
    }

    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function setScopes(array $scopes): static
    {
        $this->scopes = $scopes;

        return $this;
    }

    public function setConfidential(bool $value): static
    {
        $this->isConfidential = $value;
        return $this;
    }
}

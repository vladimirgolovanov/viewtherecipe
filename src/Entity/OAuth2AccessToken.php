<?php

declare(strict_types=1);

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

#[ApiResource(
    operations: [
        new GetCollection(security: 'is_granted("ROLE_USER")'),
        new Delete(security: 'object.getUserIdentifier() == user.getUserIdentifier()'),
    ]
)]
#[ORM\Entity]
#[ORM\Table(name: 'oauth2_access_token')]
class OAuth2AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    protected string $identifier;

    protected ClientEntityInterface $client;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    protected ?string $userIdentifier = null;

    #[ORM\Column(type: 'boolean')]
    private bool $revoked = false;

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $expiryDateTime;

    public function __construct()
    {
        $this->identifier = bin2hex(random_bytes(40));
        $this->expiryDateTime = new \DateTimeImmutable('+1 year');
        $this->scopes = [new OAuth2Scope('mcp')];
    }

    public function getClient(): ClientEntityInterface
    {
        return $this->client;
    }

    public function setClient(ClientEntityInterface $client): void
    {
        $this->client = $client;
    }

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
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

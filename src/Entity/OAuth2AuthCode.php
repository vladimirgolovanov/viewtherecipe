<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

#[ORM\Entity]
#[ORM\Table(name: 'oauth2_auth_code')]
class OAuth2AuthCode implements AuthCodeEntityInterface
{
    use AuthCodeTrait;
    use EntityTrait;
    use TokenEntityTrait;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    protected string $identifier;

    #[ORM\ManyToOne(targetEntity: OAuth2Client::class)]
    #[ORM\JoinColumn(referencedColumnName: 'identifier', nullable: false)]
    protected ClientEntityInterface $client; // @phpstan-ignore-line

    #[ORM\Column(type: 'boolean')]
    private bool $revoked = false;

    #[ORM\Column(type: 'datetime_immutable')]
    protected \DateTimeImmutable $expiryDateTime;

    public function isRevoked(): bool
    {
        return $this->revoked;
    }

    public function revoke(): void
    {
        $this->revoked = true;
    }
}


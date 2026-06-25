<?php

declare(strict_types=1);

namespace App\OAuth;

use App\Repository\AccessTokenRepository;
use App\Repository\AuthCodeRepository;
use App\Repository\ClientRepository;
use App\Repository\RefreshTokenRepository;
use App\Repository\ScopeRepository;
use League\OAuth2\Server\AuthorizationServer;
use League\OAuth2\Server\Grant\AuthCodeGrant;
use League\OAuth2\Server\Grant\RefreshTokenGrant;

class AuthorizationServerFactory
{
    public function __construct(
        private readonly ClientRepository $clientRepository,
        private readonly AccessTokenRepository $accessTokenRepository,
        private readonly ScopeRepository $scopeRepository,
        private readonly AuthCodeRepository $authCodeRepository,
        private readonly RefreshTokenRepository $refreshTokenRepository,
        private readonly string $privateKey,
        private readonly string $encryptionKey,
    ) {
    }

    public function create(): AuthorizationServer
    {
        $server = new AuthorizationServer(
            $this->clientRepository,
            $this->accessTokenRepository,
            $this->scopeRepository,
            $this->privateKey,
            $this->encryptionKey,
        );

        $server->enableGrantType(
            new AuthCodeGrant(
                $this->authCodeRepository,
                $this->refreshTokenRepository,
                new \DateInterval('PT10M'),
            ),
            new \DateInterval('PT1H'),
        );

        $server->enableGrantType(
            new RefreshTokenGrant($this->refreshTokenRepository),
            new \DateInterval('PT1H'),
        );

        return $server;
    }
}

<?php

declare(strict_types=1);

namespace App\Grant;

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Grant\AuthCodeGrant as BaseAuthCodeGrant;
use Psr\Http\Message\ServerRequestInterface;

class AuthCodeGrant extends BaseAuthCodeGrant
{
    protected function validateRedirectUri(
        string $redirectUri,
        ClientEntityInterface $client,
        ServerRequestInterface $request
    ): void {
        // redirect_uri is accepted as-is; client is the user, no pre-registered URIs
    }
}

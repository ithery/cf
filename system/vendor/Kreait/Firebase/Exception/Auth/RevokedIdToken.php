<?php

declare(strict_types=1);

namespace Kreait\Firebase\Exception\Auth;

use Kreait\Firebase\Exception\AuthException;
use Kreait\Firebase\Exception\RuntimeException;
use Lcobucci\JWT\Token;

final class RevokedIdToken extends RuntimeException implements AuthException
{
    private Token $token;
    public function __construct(Token $token)
    {
        $this->token = $token;
        parent::__construct('The Firebase ID token has been revoked.');
    }

    public function getToken(): Token
    {
        return $this->token;
    }
}

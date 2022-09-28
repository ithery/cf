<?php

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;

class CApi_OAuth_Bridge_RefreshToken implements RefreshTokenEntityInterface {
    use EntityTrait, RefreshTokenTrait;
}

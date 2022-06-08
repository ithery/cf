<?php

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\AuthCodeTrait;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

class CApi_OAuth_Bridge_AuthCode implements AuthCodeEntityInterface {
    use AuthCodeTrait, EntityTrait, TokenEntityTrait;
}

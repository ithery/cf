<?php

use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\UserEntityInterface;

class CApi_OAuth_Bridge_User implements UserEntityInterface {
    use EntityTrait;

    /**
     * Create a new user instance.
     *
     * @param string|int $identifier
     *
     * @return void
     */
    public function __construct($identifier) {
        $this->setIdentifier($identifier);
    }
}

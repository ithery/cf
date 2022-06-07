<?php

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class CApi_OAuth_Bridge_ScopeRepository implements ScopeRepositoryInterface {
    /**
     * @var CApi_OAuth
     */
    protected $oauth;

    public function __construct(CApi_OAuth $oauth) {
        $this->oauth = $oauth;
    }

    /**
     * @inheritdoc
     */
    public function getScopeEntityByIdentifier($identifier) {
        if ($this->oauth->hasScope($identifier)) {
            return new CApi_OAuth_Bridge_Scope($identifier);
        }
    }

    /**
     * @inheritdoc
     */
    public function finalizeScopes(
        array $scopes,
        $grantType,
        ClientEntityInterface $clientEntity,
        $userIdentifier = null
    ) {
        if (!in_array($grantType, ['password', 'personal_access', 'client_credentials'])) {
            $scopes = c::collect($scopes)->reject(function ($scope) {
                return trim($scope->getIdentifier()) === '*';
            })->values()->all();
        }

        return c::collect($scopes)->filter(function ($scope) {
            return $this->oauth->hasScope($scope->getIdentifier());
        })->values()->all();
    }
}

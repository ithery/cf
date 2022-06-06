<?php

use Laravel\Passport\Passport;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\ScopeRepositoryInterface;

class CApi_OAuth_Bridge_ScopeRepository implements ScopeRepositoryInterface {
    /**
     * @inheritdoc
     */
    public function getScopeEntityByIdentifier($identifier) {
        if (CApi::oauth()->hasScope($identifier)) {
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
            return CApi::oauth()->hasScope($scope->getIdentifier());
        })->values()->all();
    }
}

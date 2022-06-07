<?php

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class CApi_OAuth_Bridge_UserRepository implements UserRepositoryInterface {
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
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity) {
        $guard = $this->oauth->getGuardName();

        $provider = $clientEntity->provider ?: CF::config('auth.guards.' . $guard . '.provider');

        if (is_null($model = CF::config('auth.providers.' . $provider . '.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }
        $hasher = c::hash(CF::config('auth.providers.' . $provider . '.hasher', 'md5'));
        if (method_exists($model, 'findAndValidateForOAuth')) {
            $user = (new $model())->findAndValidateForOAuth($username, $password);

            if (!$user) {
                return;
            }

            return new CApi_OAuth_Bridge_User($user->getAuthIdentifier());
        }

        if (method_exists($model, 'findForOAuth')) {
            $user = (new $model())->findForOAuth($username);
        } else {
            $user = (new $model())->where('email', $username)->first();
        }

        if (!$user) {
            return;
        } elseif (method_exists($user, 'validateForOAuthPasswordGrant')) {
            if (!$user->validateForPassportPasswordGrant($password)) {
                return;
            }
        } elseif (!$hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        return new CApi_OAuth_Bridge_User($user->getAuthIdentifier());
    }
}

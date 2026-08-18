<?php

use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Exception\OAuthServerException;
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
            try {
                $user = (new $model())->findAndValidateForOAuth($username, $password);
            } catch (CAuth_Exception_AuthorizationException $ex) {
                throw new OAuthServerException($ex->getMessage(), 9, 'access_denied', 401, $ex->getMessage(), null, $ex);
            } catch (Throwable $e) {
                throw OAuthServerException::serverError($e->getMessage(), $e);
            }
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
            if (!$user->validateForOAuthPasswordGrant($password)) {
                return;
            }
        } elseif (!$hasher->check($password, $user->getAuthPassword())) {
            return;
        }

        return new CApi_OAuth_Bridge_User($user->getAuthIdentifier());
    }

    /**
     * @inheritdoc
     */
    public function getUserEntityBySocialLogin($socialProvider, $accessToken, $grantType, ClientEntityInterface $clientEntity) {
        $guard = $this->oauth->getGuardName();

        $provider = $clientEntity->provider ?: CF::config('auth.guards.' . $guard . '.provider');

        if (is_null($model = CF::config('auth.providers.' . $provider . '.model'))) {
            throw new RuntimeException('Unable to determine authentication model from configuration.');
        }
        $user = null;
        if (method_exists($model, 'findAndValidateForOAuthSocialLogin')) {
            try {
                $user = (new $model())->findAndValidateForOAuthSocialLogin($socialProvider, $accessToken);
            } catch (CAuth_Exception_AuthorizationException $ex) {
                throw new OAuthServerException($ex->getMessage(), 9, 'access_denied', 401, $ex->getMessage(), null, $ex);
            } catch (Throwable $e) {
                throw OAuthServerException::serverError($e->getMessage(), $e);
            }

            if (!$user) {
                return;
            }

            return new CApi_OAuth_Bridge_User($user->getAuthIdentifier());
        }

        return $user;
    }
}

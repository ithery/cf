<?php

namespace Cresenity\Demo\Api\Widget\Middleware;

/**
 * Requires the incoming token to be a password-grant token issued to a
 * specific user (client_credentials tokens have no user), and resolves that
 * user onto the request. See /docs/api/oauth ("Protecting Endpoints").
 */
class UserCredentialsMiddleware extends \CApi_OAuth_Middleware_CheckClientCredentials {
    /**
     * @param null|\CApi_OAuth_Model_OAuthAccessToken $token
     *
     * @throws \CAuth_Exception_AuthenticationException
     *
     * @return void
     */
    protected function validateCredentials($token) {
        parent::validateCredentials($token);

        $user = $token->user;

        if (!$user) {
            throw new \CAuth_Exception_AuthenticationException('This endpoint requires a user-authenticated (password grant) token.');
        }

        $this->request->setUserResolver(function () use ($user) {
            return $user;
        });
    }
}

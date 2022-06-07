<?php

class CApi_OAuth_Middleware_CheckClientCredentialsForAnyScope extends CApi_OAuth_Middleware_CheckCredentials {
    /**
     * Validate token credentials.
     *
     * @param \CApi_OAuth_Model_OAuthAccessToken $token
     *
     * @throws \CAuth_Exception_AuthenticationException
     *
     * @return void
     */
    protected function validateCredentials($token) {
        if (!$token) {
            throw new CAuth_Exception_AuthenticationException();
        }
    }

    /**
     * Validate token credentials.
     *
     * @param \CApi_OAuth_Model_OAuthAccessToken $token
     * @param array                              $scopes
     *
     * @throws \CApi_OAuth_Exception_MissingScopeException
     *
     * @return void
     */
    protected function validateScopes($token, $scopes) {
        if (in_array('*', $token->scopes)) {
            return;
        }

        foreach ($scopes as $scope) {
            if ($token->can($scope)) {
                return;
            }
        }

        throw new CApi_OAuth_Exception_MissingScopeException($scopes);
    }
}

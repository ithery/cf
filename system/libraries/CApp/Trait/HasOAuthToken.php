<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CApp_Trait_HasOAuthToken {

    /**
     * The current access token for the authentication user.
     *
     * @var COAuth_Model_AccessToken
     */
    protected $accessToken;

    /**
     * Get all of the user's registered OAuth clients.
     *
     * @return CModel_Relation_HasMany
     */
    public function clients() {
        return $this->hasMany(Passport::clientModel(), 'user_id');
    }

    /**
     * Get all of the access tokens for the user.
     *
     * @return CModel_Relation_HasMany
     */
    public function tokens() {
        return $this->hasMany(Passport::tokenModel(), 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the current access token being used by the user.
     *
     * @return COAuth_Model_AccessToken|null
     */
    public function token() {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param  string  $scope
     * @return bool
     */
    public function tokenCan($scope) {
        return $this->accessToken ? $this->accessToken->can($scope) : false;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param  string  $name
     * @param  array  $scopes
     * @return COAuth_PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = []) {
        return CContainer::getInstance()->make(PersonalAccessTokenFactory::class)->make(
                        $this->getKey(), $name, $scopes
        );
    }

    /**
     * Set the current access token for the user.
     *
     * @param  COAuth_Model_AccessToken $accessToken
     * @return $this
     */
    public function withAccesÏsToken($accessToken) {
        $this->accessToken = $accessToken;
        return $this;
    }

}

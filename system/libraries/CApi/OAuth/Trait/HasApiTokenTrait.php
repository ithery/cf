<?php

trait CApi_OAuth_Trait_HasApiTokenTrait {
    /**
     * The current access token for the authentication user.
     *
     * @var \CApi_OAuth_Model_OAuthAccessToken
     */
    protected $accessToken;

    /**
     * Get all of the user's registered OAuth clients.
     *
     * @return \CModel_Relation_HasMany
     */
    public function oauthClient() {
        return $this->hasMany(CApi::oauth()->clientModel(), 'user_id');
    }

    /**
     * Get all of the access tokens for the user.
     *
     * @return \CModel_Relation_HasMany
     */
    public function oauthAccessToken() {
        return $this->hasMany(CApi::oauth()->tokenModel(), 'user_id')->orderBy('created_at', 'desc');
    }

    /**
     * Get the current access token being used by the user.
     *
     * @return null|\CApi_OAuth_Model_OAuthAccessToken
     */
    public function token() {
        return $this->accessToken;
    }

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $scope
     *
     * @return bool
     */
    public function tokenCan($scope) {
        return $this->accessToken ? $this->accessToken->can($scope) : false;
    }

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array  $scopes
     *
     * @return \CApi_OAuth_PersonalAccessTokenResult
     */
    public function createToken($name, array $scopes = []) {
        return c::container()->make(CApi_OAuth_PersonalAccessTokenFactory::class)->make(
            $this->getKey(),
            $name,
            $scopes
        );
    }

    /**
     * Set the current access token for the user.
     *
     * @param \CApi_OAuth_Model_OAuthAccessToken $accessToken
     *
     * @return $this
     */
    public function withAccessToken($accessToken) {
        $this->accessToken = $accessToken;

        return $this;
    }
}

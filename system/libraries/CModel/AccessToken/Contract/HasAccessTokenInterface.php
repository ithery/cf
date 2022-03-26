<?php
interface CModel_AccessToken_Contract_HasAccessTokenInterface {
    /**
     * Get the access tokens that belong to model.
     *
     * @return CModel_Relation_MorphMany
     */
    public function accessToken();

    /**
     * Determine if the current API token has a given scope.
     *
     * @param string $ability
     *
     * @return bool
     */
    public function accessTokenCan($ability);

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array  $abilities
     *
     * @return \CModel_AccessToken_NewAccessToken
     */
    public function createAccessToken(string $name, array $abilities = ['*']);

    /**
     * Get the access token currently associated with the user.
     *
     * @return \CModel_AccessToken_Contract_HasAbilitiesInterface
     */
    public function currentAccessToken();

    /**
     * Set the current access token for the user.
     *
     * @param \CModel_AccessToken_Contract_HasAbilitiesInterface $accessToken
     *
     * @return \CModel_AccessToken_Contract_HasAccessTokenInterface
     */
    public function withAccessToken($accessToken);
}

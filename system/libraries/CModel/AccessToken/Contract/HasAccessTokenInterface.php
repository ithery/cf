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
    public function tokenCan($ability);

    /**
     * Create a new personal access token for the user.
     *
     * @param string $name
     * @param array  $abilities
     *
     * @return \Laravel\Sanctum\NewAccessToken
     */
    public function createToken(string $name, array $abilities = ['*']);

    /**
     * Get the access token currently associated with the user.
     *
     * @return \Laravel\Sanctum\Contracts\HasAbilities
     */
    public function currentAccessToken();

    /**
     * Set the current access token for the user.
     *
     * @param \Laravel\Sanctum\Contracts\HasAbilities $accessToken
     *
     * @return \Laravel\Sanctum\Contracts\HasApiTokens
     */
    public function withAccessToken($accessToken);
}

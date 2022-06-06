<?php

class CApi_OAuth_TokenRepository {
    /**
     * Creates a new Access Token.
     *
     * @param array $attributes
     *
     * @return \CApi_OAuth_Model_OAuthAccessToken
     */
    public function create($attributes) {
        return CApi::oauth()->token()->create($attributes);
    }

    /**
     * Get a token by the given ID.
     *
     * @param string $id
     *
     * @return \CApi_OAuth_Model_OAuthAccessToken
     */
    public function find($id) {
        return CApi::oauth()->token()->where('id', $id)->first();
    }

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param string $id
     * @param int    $userId
     *
     * @return null|\CApi_OAuth_Model_OAuthAccessToken
     */
    public function findForUser($id, $userId) {
        return CApi::oauth()->token()->where('id', $id)->where('user_id', $userId)->first();
    }

    /**
     * Get the token instances for the given user ID.
     *
     * @param mixed $userId
     *
     * @return \CModel_Collection
     */
    public function forUser($userId) {
        return CApi::oauth()->token()->where('user_id', $userId)->get();
    }

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \CApi_OAuth_Model_OAuthClient       $client
     *
     * @return null|\CApi_OAuth_Model_OAuthAccessToken
     */
    public function getValidToken($user, $client) {
        return $client->tokens()
            ->whereUserId($user->getAuthIdentifier())
            ->where('revoked', 0)
            ->where('expires_at', '>', CCarbon::now())
            ->first();
    }

    /**
     * Store the given token instance.
     *
     * @param \CApi_OAuth_Model_OAuthAccessToken $token
     *
     * @return void
     */
    public function save(CApi_OAuth_Model_OAuthAccessToken $token) {
        $token->save();
    }

    /**
     * Revoke an access token.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function revokeAccessToken($id) {
        return CApi::oauth()->token()->where('id', $id)->update(['revoked' => true]);
    }

    /**
     * Check if the access token has been revoked.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isAccessTokenRevoked($id) {
        if ($token = $this->find($id)) {
            return $token->revoked;
        }

        return true;
    }

    /**
     * Find a valid token for the given user and client.
     *
     * @param \Illuminate\Database\Eloquent\Model $user
     * @param \CApi_OAuth_Model_OAuthClient       $client
     *
     * @return null|\CApi_OAuth_Model_OAuthAccessToken
     */
    public function findValidToken($user, $client) {
        return $client->tokens()
            ->whereUserId($user->getAuthIdentifier())
            ->where('revoked', 0)
            ->where('expires_at', '>', CCarbon::now())
            ->latest('expires_at')
            ->first();
    }
}

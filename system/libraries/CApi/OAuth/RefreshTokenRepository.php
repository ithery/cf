<?php

class CApi_OAuth_RefreshTokenRepository {
    /**
     * CApi OAuth.
     *
     * @var CApi_OAuth
     */
    protected $oauth;

    /**
     * Create a new refresh token repository.
     *
     * @return void
     */
    public function __construct(CApi_OAuth $oauth) {
        $this->oauth = $oauth;
    }

    /**
     * Creates a new refresh token.
     *
     * @param array $attributes
     *
     * @return \CApi_OAuth_Model_OAuthRefreshToken
     */
    public function create($attributes) {
        return $this->oauth->refreshToken()->create($attributes);
    }

    /**
     * Gets a refresh token by the given ID.
     *
     * @param string $id
     *
     * @return \CApi_OAuth_Model_OAuthRefreshToken
     */
    public function find($id) {
        return $this->oauth->refreshToken()->where('id', $id)->first();
    }

    /**
     * Stores the given token instance.
     *
     * @param \CApi_OAuth_Model_OAuthRefreshToken $token
     *
     * @return void
     */
    public function save(CApi_OAuth_Model_OAuthRefreshToken $token) {
        $token->save();
    }

    /**
     * Revokes the refresh token.
     *
     * @param string $id
     *
     * @return mixed
     */
    public function revokeRefreshToken($id) {
        return $this->oauth->refreshToken()->where('id', $id)->update(['revoked' => true]);
    }

    /**
     * Revokes refresh tokens by access token id.
     *
     * @param string $tokenId
     *
     * @return void
     */
    public function revokeRefreshTokensByAccessTokenId($tokenId) {
        $this->oauth->refreshToken()->where('access_token_id', $tokenId)->update(['revoked' => true]);
    }

    /**
     * Checks if the refresh token has been revoked.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isRefreshTokenRevoked($id) {
        if ($token = $this->find($id)) {
            return $token->revoked;
        }

        return true;
    }

    public function oauth() {
        return $this->oauth;
    }
}

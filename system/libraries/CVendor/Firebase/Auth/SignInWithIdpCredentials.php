<?php

final class CVendor_Firebase_Auth_SignInWithIdpCredentials implements CVendor_Firebase_Auth_IsTenantAwareInterface, CVendor_Firebase_Auth_SignInInterface {
    /**
     * @var string
     */
    private $provider;

    /**
     * @var null|string
     */
    private $accessToken = null;

    /**
     * @var null|string
     */
    private $idToken = null;

    /**
     * @var null|string
     */
    private $linkingIdToken = null;

    /**
     * @var null|string
     */
    private $oauthTokenSecret = null;

    /**
     * @var null|string
     */
    private $rawNonce = null;

    /**
     * @var string
     */
    private $requestUri = 'http://localhost';

    /**
     * @var null|string
     */
    private $tenantId = null;

    /**
     * @param string $provider
     */
    private function __construct($provider) {
        $this->provider = $provider;
    }

    /**
     * @param string $provider
     * @param string $accessToken
     *
     * @return self
     */
    public static function withAccessToken($provider, $accessToken) {
        $instance = new self($provider);
        $instance->accessToken = $accessToken;

        return $instance;
    }

    /**
     * @param string $provider
     * @param string $accessToken
     * @param string $oauthTokenSecret
     *
     * @return self
     */
    public static function withAccessTokenAndOauthTokenSecret($provider, $accessToken, $oauthTokenSecret) {
        $instance = self::withAccessToken($provider, $accessToken);
        $instance->oauthTokenSecret = $oauthTokenSecret;

        return $instance;
    }

    /**
     * @param string $provider
     * @param string $idToken
     *
     * @return self
     */
    public static function withIdToken($provider, $idToken) {
        $instance = new self($provider);
        $instance->idToken = $idToken;

        return $instance;
    }

    /**
     * @param string $rawNonce
     *
     * @return self
     */
    public function withRawNonce($rawNonce) {
        $instance = clone $this;
        $instance->rawNonce = $rawNonce;

        return $instance;
    }

    /**
     * @param string $idToken
     *
     * @return self
     */
    public function withLinkingIdToken($idToken) {
        $instance = clone $this;
        $instance->linkingIdToken = $idToken;

        return $instance;
    }

    /**
     * @param string $requestUri
     *
     * @return self
     */
    public function withRequestUri($requestUri) {
        $instance = clone $this;
        $instance->requestUri = $requestUri;

        return $instance;
    }

    /**
     * @param string $tenantId
     *
     * @return self
     */
    public function withTenantId($tenantId) {
        $action = clone $this;
        $action->tenantId = $tenantId;

        return $action;
    }

    /**
     * @return string
     */
    public function provider() {
        return $this->provider;
    }

    /**
     * @return null|string
     */
    public function oauthTokenSecret() {
        return $this->oauthTokenSecret;
    }

    /**
     * @return null|string
     */
    public function accessToken() {
        return $this->accessToken;
    }

    /**
     * @return null|string
     */
    public function idToken() {
        return $this->idToken;
    }

    /**
     * @return null|string
     */
    public function rawNonce() {
        return $this->rawNonce;
    }

    /**
     * @return null|string
     */
    public function linkingIdToken() {
        return $this->linkingIdToken;
    }

    /**
     * @return string
     */
    public function requestUri() {
        return $this->requestUri;
    }

    /**
     * @return null|string
     */
    public function tenantId() {
        return $this->tenantId;
    }
}

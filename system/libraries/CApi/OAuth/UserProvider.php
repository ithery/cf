<?php

class CApi_OAuth_UserProvider implements CAuth_UserProviderInterface {
    /**
     * The user provider instance.
     *
     * @var \CAuth_UserProviderInterface
     */
    protected $provider;

    /**
     * The user provider name.
     *
     * @var string
     */
    protected $providerName;

    /**
     * Create a new passport user provider.
     *
     * @param \CAuth_UserProviderInterface $provider
     * @param string                       $providerName
     *
     * @return void
     */
    public function __construct(CAuth_UserProviderInterface $provider, $providerName) {
        $this->provider = $provider;
        $this->providerName = $providerName;
    }

    /**
     * @inheritdoc
     */
    public function retrieveById($identifier) {
        return $this->provider->retrieveById($identifier);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByObject($obj) {
        return $this->provider->retrieveById(c::optional($obj)->user_id);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByToken($identifier, $token) {
        return $this->provider->retrieveByToken($identifier, $token);
    }

    /**
     * @inheritdoc
     */
    public function updateRememberToken($user, $token) {
        $this->provider->updateRememberToken($user, $token);
    }

    /**
     * @inheritdoc
     */
    public function retrieveByCredentials(array $credentials) {
        return $this->provider->retrieveByCredentials($credentials);
    }

    /**
     * @inheritdoc
     */
    public function validateCredentials(CAuth_AuthenticatableInterface $user, array $credentials) {
        return $this->provider->validateCredentials($user, $credentials);
    }

    /**
     * Get the name of the user provider.
     *
     * @return string
     */
    public function getProviderName() {
        return $this->providerName;
    }

    public function hasher() {
        return $this->provider->hasher();
    }
}

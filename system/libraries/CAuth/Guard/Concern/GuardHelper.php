
<?php

/**
 * These methods are typically the same across all guards.
 */
trait CAuth_Guard_Concern_GuardHelper {
    /**
     * The currently authenticated user.
     *
     * @var CAuth_AuthenticatableInterface
     */
    protected $user;

    /**
     * The user provider implementation.
     *
     * @var CAuth_UserProviderInterface
     */
    protected $provider;

    /**
     * Determine if current user is authenticated. If not, throw an exception.
     *
     * @return \Illuminate\Contracts\Auth\Authenticatable
     *
     * @throws \Illuminate\Auth\AuthenticationException
     */
    public function authenticate() {
        if (!is_null($user = $this->user())) {
            return $user;
        }

        throw new CAuth_Exception_AuthenticationException;
    }

    /**
     * Determine if the guard has a user instance.
     *
     * @return bool
     */
    public function hasUser() {
        return !is_null($this->user);
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check() {
        return !is_null($this->user());
    }

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest() {
        return !$this->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
     */
    public function id() {
        if ($this->user()) {
            return $this->user()->getAuthIdentifier();
        }
    }

    /**
     * Set the current user.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return $this
     */
    public function setUser(CAuth_AuthenticatableInterface $user) {
        $this->user = $user;

        return $this;
    }

    /**
     * Get the user provider used by the guard.
     *
     * @return CAuth_UserProviderInterface
     */
    public function getProvider() {
        return $this->provider;
    }

    /**
     * Set the user provider used by the guard.
     *
     * @param CAuth_UserProviderInterface $provider
     *
     * @return void
     */
    public function setProvider(CAuth_UserProviderInterface $provider) {
        $this->provider = $provider;
    }
}

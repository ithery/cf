<?php

trait CTesting_Concern_InteractsWithAuthentication {
    /**
     * Set the currently logged in user for the application.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param null|string                     $guard
     *
     * @return $this
     */
    public function actingAs(CAuth_AuthenticatableInterface $user, $guard = null) {
        return $this->be($user, $guard);
    }

    /**
     * Set the currently logged in user for the application.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param null|string                     $guard
     *
     * @return $this
     */
    public function be(CAuth_AuthenticatableInterface $user, $guard = null) {
        if (isset($user->wasRecentlyCreated) && $user->wasRecentlyCreated) {
            $user->wasRecentlyCreated = false;
        }

        c::auth()->guard($guard)->setUser($user);

        c::auth()->shouldUse($guard);

        return $this;
    }

    /**
     * Assert that the user is authenticated.
     *
     * @param null|string $guard
     *
     * @return $this
     */
    public function assertAuthenticated($guard = null) {
        $this->assertTrue($this->isAuthenticated($guard), 'The user is not authenticated');

        return $this;
    }

    /**
     * Assert that the user is not authenticated.
     *
     * @param null|string $guard
     *
     * @return $this
     */
    public function assertGuest($guard = null) {
        $this->assertFalse($this->isAuthenticated($guard), 'The user is authenticated');

        return $this;
    }

    /**
     * Return true if the user is authenticated, false otherwise.
     *
     * @param null|string $guard
     *
     * @return bool
     */
    protected function isAuthenticated($guard = null) {
        return c::auth()->guard($guard)->check();
    }

    /**
     * Assert that the user is authenticated as the given user.
     *
     * @param \CAuth_AuthenticatableInterface $user
     * @param null|string                     $guard
     *
     * @return $this
     */
    public function assertAuthenticatedAs($user, $guard = null) {
        $expected = c::auth()->guard($guard)->user();

        $this->assertNotNull($expected, 'The current user is not authenticated.');

        $this->assertInstanceOf(
            get_class($expected),
            $user,
            'The currently authenticated user is not who was expected'
        );

        $this->assertSame(
            $expected->getAuthIdentifier(),
            $user->getAuthIdentifier(),
            'The currently authenticated user is not who was expected'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are valid.
     *
     * @param array       $credentials
     * @param null|string $guard
     *
     * @return $this
     */
    public function assertCredentials(array $credentials, $guard = null) {
        $this->assertTrue(
            $this->hasCredentials($credentials, $guard),
            'The given credentials are invalid.'
        );

        return $this;
    }

    /**
     * Assert that the given credentials are invalid.
     *
     * @param array       $credentials
     * @param null|string $guard
     *
     * @return $this
     */
    public function assertInvalidCredentials(array $credentials, $guard = null) {
        $this->assertFalse(
            $this->hasCredentials($credentials, $guard),
            'The given credentials are valid.'
        );

        return $this;
    }

    /**
     * Return true if the credentials are valid, false otherwise.
     *
     * @param array       $credentials
     * @param null|string $guard
     *
     * @return bool
     */
    protected function hasCredentials(array $credentials, $guard = null) {
        $provider = c::auth()->guard($guard)->getProvider();

        $user = $provider->retrieveByCredentials($credentials);

        return $user && $provider->validateCredentials($user, $credentials);
    }
}

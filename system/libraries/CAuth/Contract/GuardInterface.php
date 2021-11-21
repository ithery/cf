<?php

interface CAuth_Contract_GuardInterface {
    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check();

    /**
     * Determine if the current user is a guest.
     *
     * @return bool
     */
    public function guest();

    /**
     * Get the currently authenticated user.
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function user();

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return null|int|string
     */
    public function id();

    /**
     * Validate a user's credentials.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function validate(array $credentials = []);

    /**
     * Set the current user.
     *
     * @param CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    public function setUser(CAuth_AuthenticatableInterface $user);

    /**
     * Get the user provider used by the guard.
     *
     * @return CAuth_UserProviderInterface
     */
    public function getProvider();
}

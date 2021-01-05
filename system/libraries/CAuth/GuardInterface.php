<?php

interface CAuth_GuardInterface {
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
     * @return CAuth_AuthenticatableInterface|null
     */
    public function user();

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return int|string|null
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
}

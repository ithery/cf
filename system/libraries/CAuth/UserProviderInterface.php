<?php

/**
 * Interface of UserProvider
 *
 * see implementation below
 *
 * @see CAuth_UserProvider_DatabaseUserProvider
 * @see CAuth_UserProvider_ModelUserProvider
 */

interface CAuth_UserProviderInterface {
    /**
     * Retrieve a user by their unique identifier.
     *
     * @param mixed $identifier
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveById($identifier);

    /**
     * Retrieve a user by their unique identifier and "remember me" token.
     *
     * @param mixed  $identifier
     * @param string $token
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveByToken($identifier, $token);

    /**
     * Update the "remember me" token for the given user in storage.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param string                         $token
     *
     * @return void
     */
    public function updateRememberToken($user, $token);

    /**
     * Retrieve a user by the given credentials.
     *
     * @param array $credentials
     *
     * @return CAuth_AuthenticatableInterface|null
     */
    public function retrieveByCredentials(array $credentials);

    /**
     * Validate a user against the given credentials.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param array                          $credentials
     *
     * @return bool
     */
    public function validateCredentials(CAuth_AuthenticatableInterface $user, array $credentials);
}

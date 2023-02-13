<?php
interface CAuth_Contract_StatefulGuardInterface extends CAuth_Contract_GuardInterface {
    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false);

    /**
     * Log a user into the application without sessions or cookies.
     *
     * @param array $credentials
     *
     * @return bool
     */
    public function once(array $credentials = []);

    /**
     * Log a user into the application.
     *
     * @param CAuth_AuthenticatableInterface $user
     * @param bool                           $remember
     *
     * @return void
     */
    public function login(CAuth_AuthenticatableInterface $user, $remember = false);

    /**
     * Log the given user ID into the application.
     *
     * @param mixed $id
     * @param bool  $remember
     *
     * @return CAuth_AuthenticatableInterface|bool
     */
    public function loginUsingId($id, $remember = false);

    /**
     * Log the given user ID into the application without sessions or cookies.
     *
     * @param mixed $id
     *
     * @return CAuth_AuthenticatableInterface|bool
     */
    public function onceUsingId($id);

    /**
     * Determine if the user was authenticated via "remember me" cookie.
     *
     * @return bool
     */
    public function viaRemember();

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout();

    /**
     * Get the hasher for current guard.
     *
     * @return CCrypt_HasherInterface
     */
    public function hasher();

    /**
     * Log a user into the application without firing the Login event.
     *
     * @param \CAuth_AuthenticatableInterface $user
     *
     * @return void
     */
    public function quietLogin(CAuth_AuthenticatableInterface $user);

    /**
     * Logout the user without updating remember_token
     * and without firing the Logout event.
     *
     * @return void
     */
    public function quietLogout();
}

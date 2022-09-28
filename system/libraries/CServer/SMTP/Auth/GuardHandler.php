<?php

class CServer_SMTP_Auth_GuardHandler extends CServer_SMTP_Auth_Handler {
    /**
     * The guard used to authenticate the user.
     *
     * @var CAuth_Contract_GuardInterface
     */
    protected $guard;

    /**
     * EloquentUserHandler constructor.
     *
     * @param $guard string|null
     */
    public function __construct($guard = null) {
        $this->guard = CAuth::manager()->guard($guard);
    }

    /**
     * Attempt to authenticate a user when logging in via SMTP.
     *
     * @param array $credentials
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function attempt(array $credentials) {
        /**
         * Clone the guard so the authenticated user is not carried on to the next connection.
         * It's a bit of a hacky solution but required since the guards are designed for a single request lifetime.
         */
        $guard = clone $this->guard;

        if ($guard->validate($credentials)) {
            return $guard->user();
        }

        return null;
    }
}

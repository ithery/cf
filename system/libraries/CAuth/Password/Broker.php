<?php

class CAuth_Password_Broker implements CAuth_Contract_PasswordBrokerInterface {
    /**
     * The password token repository.
     *
     * @var \CAuth_Contract_TokenRepositoryInterface
     */
    protected $tokens;

    /**
     * The user provider implementation.
     *
     * @var \CAuth_UserProviderInterface
     */
    protected $users;

    /**
     * Create a new password broker instance.
     *
     * @param \CAuth_Contract_TokenRepositoryInterface $tokens
     * @param \CAuth_UserProviderInterface             $users
     *
     * @return void
     */
    public function __construct(CAuth_Contract_TokenRepositoryInterface $tokens, CAuth_UserProviderInterface $users) {
        $this->users = $users;
        $this->tokens = $tokens;
    }

    /**
     * Send a password reset link to a user.
     *
     * @param array         $credentials
     * @param null|\Closure $callback
     *
     * @return string
     */
    public function sendResetLink(array $credentials, $callback = null) {
        // First we will check to see if we found a user at the given credentials and
        // if we did not we will redirect back to this current URI with a piece of
        // "flash" data in the session to indicate to the developers the errors.
        $user = $this->getUser($credentials);

        if (is_null($user)) {
            return static::INVALID_USER;
        }

        if ($this->tokens->recentlyCreatedToken($user)) {
            return static::RESET_THROTTLED;
        }

        $token = $this->tokens->create($user);

        if ($callback) {
            $callback($user, $token);
        } else {
            // Once we have the reset token, we are ready to send the message out to this
            // user with a link to reset their password. We will then redirect back to
            // the current URI having nothing set in the session to indicate errors.
            $user->sendPasswordResetNotification($token);
        }

        return static::RESET_LINK_SENT;
    }

    /**
     * Reset the password for the given token.
     *
     * @param array    $credentials
     * @param \Closure $callback
     *
     * @return mixed
     */
    public function reset(array $credentials, $callback) {
        $user = $this->validateReset($credentials);

        // If the responses from the validate method is not a user instance, we will
        // assume that it is a redirect and simply return it from this method and
        // the user is properly redirected having an error message on the post.
        if (!$user instanceof CAuth_Contract_CanResetPasswordInterface) {
            return $user;
        }

        $password = $credentials['password'];

        // Once the reset has been validated, we'll call the given callback with the
        // new password. This gives the user an opportunity to store the password
        // in their persistent storage. Then we'll delete the token and return.
        $callback($user, $password);

        $this->tokens->delete($user);

        return static::PASSWORD_RESET;
    }

    /**
     * Validate a password reset for the given credentials.
     *
     * @param array $credentials
     *
     * @return \CAuth_Contract_CanResetPasswordInterface|string
     */
    protected function validateReset(array $credentials) {
        if (is_null($user = $this->getUser($credentials))) {
            return static::INVALID_USER;
        }

        if (!$this->tokens->exists($user, $credentials['token'])) {
            return static::INVALID_TOKEN;
        }

        return $user;
    }

    /**
     * Get the user for the given credentials.
     *
     * @param array $credentials
     *
     * @throws \UnexpectedValueException
     *
     * @return null|\CAuth_Contract_CanResetPasswordInterface
     */
    public function getUser(array $credentials) {
        $credentials = carr::except($credentials, ['token']);

        $user = $this->users->retrieveByCredentials($credentials);

        if ($user && !$user instanceof CAuth_Contract_CanResetPasswordInterface) {
            throw new UnexpectedValueException('User must implement CanResetPassword interface.');
        }

        return $user;
    }

    /**
     * Create a new password reset token for the given user.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return string
     */
    public function createToken(CAuth_Contract_CanResetPasswordInterface $user) {
        return $this->tokens->create($user);
    }

    /**
     * Delete password reset tokens of the given user.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     *
     * @return void
     */
    public function deleteToken(CAuth_Contract_CanResetPasswordInterface $user) {
        $this->tokens->delete($user);
    }

    /**
     * Validate the given password reset token.
     *
     * @param \CAuth_Contract_CanResetPasswordInterface $user
     * @param string                                    $token
     *
     * @return bool
     */
    public function tokenExists(CAuth_Contract_CanResetPasswordInterface $user, $token) {
        return $this->tokens->exists($user, $token);
    }

    /**
     * Get the password reset token repository implementation.
     *
     * @return \CAuth_Contract_TokenRepositoryInterface
     */
    public function getRepository() {
        return $this->tokens;
    }
}

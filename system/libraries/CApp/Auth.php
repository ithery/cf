<?php

class CApp_Auth {
    /**
     * The callback that is responsible for building the authentication pipeline array, if applicable.
     *
     * @var null|callable
     */
    public static $authenticateThroughCallback;

    /**
     * The callback that is responsible for validating authentication credentials, if applicable.
     *
     * @var null|callable
     */
    public static $authenticateUsingCallback;

    /**
     * The callback that is responsible for confirming user passwords.
     *
     * @var null|callable
     */
    public static $confirmPasswordsUsingCallback;

    protected $loginView;

    protected $twoFactorChallengeView;

    protected $registerView;

    protected $resetPasswordView;

    protected $confirmPasswordView;

    protected $requestPasswordResetLinkView;

    protected $features;

    /**
     * @var string
     */
    protected $guard;

    /**
     * @var CAuth_Contract_StatefulGuardInterface
     */
    protected $resolvedGuard;

    /**
     * @var array
     */
    private static $instance;

    public static function instance($guard) {
        if (static::$instance == null) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$guard])) {
            static::$instance[$guard] = new static($guard);
        }

        return static::$instance[$guard];
    }

    public function __construct($guard) {
        $this->guard = $guard;
        $this->features = new CApp_Auth_Features();
    }

    /**
     * Get Auth Features Instance.
     *
     * @return CApp_Auth_Features;
     */
    public function features() {
        return $this->features;
    }

    /**
     * Specify which view should be used as the login view.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setLoginView($view) {
        $this->loginView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the two factor authentication challenge view.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setTwoFactorChallengeView($view) {
        $this->twoFactorChallengeView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the new password view.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setResetPasswordView($view) {
        $this->resetPasswordView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the registration view.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setRegisterView($view) {
        $this->registerView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the email verification prompt.
     *
     * @param callable|string $view
     *
     * @return void
     */
    public function setVerifyEmailView($view) {
        $this->verifyEmailView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the password confirmation prompt.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setConfirmPasswordView($view) {
        $this->confirmPasswordView = $view;

        return $this;
    }

    /**
     * Specify which view should be used as the request password reset link view.
     *
     * @param callable|string $view
     *
     * @return CApp_Auth
     */
    public function setRequestPasswordResetLinkView($view) {
        $this->requestPasswordResetLinkView = $view;

        return $this;
    }

    /**
     * Get the name of the email address request variable / field.
     *
     * @return string
     */
    public static function email() {
        return CF::config('app.auth.email', 'email');
    }

    /**
     * Get the username used for authentication.
     *
     * @return string
     */
    public static function username() {
        return CF::config('app.auth.username', 'username');
    }

    /**
     * @return CAuth_Contract_StatefulGuardInterface
     */
    public function guard() {
        if ($this->resolvedGuard === null) {
            $this->resolvedGuard = c::auth($this->guard);
        }

        return $this->resolvedGuard;
    }

    public static function loginRateLimiter() {
        return new CApp_Auth_LoginRateLimiter(new CCache_RateLimiter(CCache::repository()));
    }

    public function attempt(array $credentials = [], $remember = false) {
        return $this->guard()->attempt($credentials, $remember);
    }

    public function logout() {
        return $this->guard()->logout();
    }

    public function user() {
        return $this->guard()->user();
    }

    public function hasher() {
        return $this->guard()->hasher();
    }
}

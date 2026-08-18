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

    /**
     * @var null|callable|string
     */
    protected $loginView;

    /**
     * @var null|callable|string
     */
    protected $twoFactorChallengeView;

    /**
     * @var null|callable|string
     */
    protected $registerView;

    /**
     * @var null|callable|string
     */
    protected $resetPasswordView;

    /**
     * @var null|callable|string
     */
    protected $confirmPasswordView;

    /**
     * @var null|callable|string
     */
    protected $verifyEmailView;

    /**
     * @var null|callable|string
     */
    protected $requestPasswordResetLinkView;

    /**
     * @var CApp_Auth_Features
     */
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
     * @var null|array
     */
    protected $resolvedGuardConfig;

    /**
     * @var null|array
     */
    protected $resolvedProviderConfig;

    /**
     * @var null|string
     */
    protected $resolvedRoleModelClass;

    /**
     * @var null|string
     */
    protected $resolvedRoleNavModelClass;

    /**
     * @var null|string
     */
    protected $resolvedRolePermissionModelClass;

    /**
     * @var array
     */
    private static $instance;

    /**
     * Get (or create) the singleton CApp_Auth instance for the given guard.
     *
     * @param string $guard
     *
     * @return CApp_Auth
     */
    public static function instance($guard) {
        if (static::$instance == null) {
            static::$instance = [];
        }
        if (!isset(static::$instance[$guard])) {
            static::$instance[$guard] = new static($guard);
        }

        return static::$instance[$guard];
    }

    /**
     * Drop all cached per-guard CApp_Auth instances (and the
     * CAuth_Contract_StatefulGuardInterface each one caches internally in
     * $resolvedGuard), so the next instance()/guard() call resolves fresh
     * instead of reusing a guard object that CAuth_Manager::forgetGuards()
     * has already orphaned.
     *
     * This class is a process-wide singleton, which is harmless in
     * production (fresh PHP process per request) but means a user set via
     * actingAs() in one test leaks into every later test in the same run -
     * see CTesting_TestCase::tearDown().
     *
     * @return void
     */
    public static function forgetInstances() {
        static::$instance = null;
    }

    /**
     * @param string $guard
     */
    public function __construct($guard) {
        $this->guard = $guard;
        $this->features = new CApp_Auth_Features();
    }

    /**
     * Get Auth Features Instance.
     *
     * @return CApp_Auth_Features
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

    /**
     * @return string
     */
    public function guardName() {
        return $this->guard;
    }

    /**
     * Create a new login rate limiter instance.
     *
     * @return CApp_Auth_LoginRateLimiter
     */
    public static function loginRateLimiter() {
        return new CApp_Auth_LoginRateLimiter(new CCache_RateLimiter(c::cache()->store()));
    }

    /**
     * Attempt to authenticate a user using the given credentials.
     *
     * @param array $credentials
     * @param bool  $remember
     *
     * @return bool
     */
    public function attempt(array $credentials = [], $remember = false) {
        return $this->guard()->attempt($credentials, $remember);
    }

    /**
     * Log the user out of the application.
     *
     * @return void
     */
    public function logout() {
        return $this->guard()->logout();
    }

    /**
     * Get the currently authenticated user.
     *
     * @return null|CAuth_AuthenticatableInterface
     */
    public function user() {
        return $this->guard()->user();
    }

    /**
     * Get the hasher for current guard.
     *
     * @return CCrypt_HasherInterface
     */
    public function hasher() {
        return $this->guard()->hasher();
    }

    /**
     * Determine if the current user is authenticated.
     *
     * @return bool
     */
    public function check() {
        return $this->guard()->check();
    }

    /**
     * Get the ID for the currently authenticated user.
     *
     * @return null|int|string
     */
    public function id() {
        return $this->guard()->id();
    }

    /**
     * Get Role Model should be used by CApp.
     *
     * @return string
     */
    public function getRoleModelClass() {
        if ($this->resolvedRoleModelClass === null) {
            $this->resolvedRoleModelClass = carr::get($this->getProviderConfig(), 'access.role.model', CF::config('app.model.role', CApp_Model_Roles::class));
        }

        return $this->resolvedRoleModelClass;
    }

    /**
     * Get Role Permission Model should be used by CApp.
     *
     * @return string
     */
    public function getRolePermisionModelClass() {
        if ($this->resolvedRolePermissionModelClass === null) {
            $this->resolvedRolePermissionModelClass = carr::get($this->getProviderConfig(), 'access.role_permission.model', CF::config('app.model.role_permission', CApp_Model_RolePermission::class));
        }

        return $this->resolvedRolePermissionModelClass;
    }

    /**
     * Get Role Permission Model should be used by CApp.
     *
     * @return string
     */
    public function getRoleNavModelClass() {
        if ($this->resolvedRoleNavModelClass === null) {
            $this->resolvedRoleNavModelClass = carr::get($this->getProviderConfig(), 'access.role_nav.model', CF::config('app.model.role_nav', CApp_Model_RoleNav::class));
        }

        return $this->resolvedRoleNavModelClass;
    }

    /**
     * Get the configuration array for the current guard.
     *
     * @return null|array
     */
    public function getGuardConfig() {
        if ($this->resolvedGuardConfig === null) {
            $this->resolvedGuardConfig = CF::config('auth.guards.' . $this->guard);
        }

        return $this->resolvedGuardConfig;
    }

    /**
     * Get the configuration array for the current guard's provider.
     *
     * @return array
     */
    public function getProviderConfig() {
        if ($this->resolvedProviderConfig === null) {
            $authConfig = CF::config('auth.providers.' . carr::get($this->getGuardConfig(), 'provider'), []);
            $appConfig = CF::config('app.auth.providers.' . carr::get($this->getGuardConfig(), 'provider'), []);

            $this->resolvedProviderConfig = carr::merge($authConfig, $appConfig);
        }

        return $this->resolvedProviderConfig;
    }
}

<?php

final class CAuth_Manager implements CAuth_Contract_AuthFactoryInterface {
    use CAuth_Concern_CreateUserProvider;

    /**
     * The registered custom driver creators.
     *
     * @var array
     */
    protected $customCreators = [];

    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $guards = [];

    /**
     * The user resolver shared by various services.
     *
     * Determines the default user for Gate, Request, and the Authenticatable contract.
     *
     * @var \Closure
     */
    protected $userResolver;

    protected $defaultGuard;

    private static $instance;

    /**
     * Get current singleton instance.
     *
     * @return CAuth_Manager
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Create a new Auth manager instance.
     *
     * @return void
     */
    private function __construct() {
        $this->userResolver = function ($guard = null) {
            return $this->guard($guard)->user();
        };
        $this->defaultGuard = CF::config('auth.defaults.guard');
    }

    /**
     * Attempt to get the guard from the local cache.
     *
     * @param null|string $name
     *
     * @return CAuth_Contract_StatefulGuardInterface|CAuth_Contract_GuardInterface
     */
    public function guard($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        if (!isset($this->guards[$name])) {
            $this->guards[$name] = $this->resolve($name);
        }
        return $this->guards[$name];
    }

    /**
     * Resolve the given guard.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \CAuth_Contract_GuardInterface|\CAuth_Contract_StatefulGuardInterface
     */
    protected function resolve($name) {
        $config = $this->getConfig($name);
        if (is_null($config)) {
            throw new InvalidArgumentException("Auth guard [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->callCustomCreator($name, $config);
        }

        $driverMethod = 'create' . ucfirst($config['driver']) . 'Driver';

        if (method_exists($this, $driverMethod)) {
            return $this->{$driverMethod}($name, $config);
        }
        if (class_exists($config['driver'])) {
            return $this->createCustomClassDriver($config['driver'], $name, $config);
        }

        throw new InvalidArgumentException(
            "Auth driver [{$config['driver']}] for guard [{$name}] is not defined."
        );
    }

    /**
     * Call a custom driver creator.
     *
     * @param string $name
     * @param array  $config
     *
     * @return mixed
     */
    protected function callCustomCreator($name, array $config) {
        return $this->customCreators[$config['driver']]($name, $config);
    }

    /**
     * Call a custom class driver creator.
     *
     * @param string $driver
     * @param string $name
     * @param array  $config
     *
     * @return mixed
     */
    protected function createCustomClassDriver($driver, $name, array $config) {
        return new $driver($name, $config);
    }

    /**
     * Create a session based authentication guard.
     *
     * @param string $name
     * @param array  $config
     *
     * @return \CAuth_Guard_SessionGuard
     */
    public function createSessionDriver($name, $config) {
        // $providerSessionName = carr::get($config, 'providerSessionName', 'user_provider');
        $provider = carr::get($config, 'provider', null);
        $provider = $this->createUserProvider($provider);
        $guard = new CAuth_Guard_SessionGuard($name, $provider, c::session());
        // When using the remember me functionality of the authentication services we
        // will need to be set the encryption instance of the guard, which allows
        // secure, encrypted cookie values to get generated for those cookies.
        if (method_exists($guard, 'setCookieJar')) {
            $guard->setCookieJar(CHTTP::cookie());
        }

        if (method_exists($guard, 'setDispatcher')) {
            $guard->setDispatcher(CEvent::dispatcher());
        }

        if (method_exists($guard, 'setRequest')) {
            //$this->app->refresh('request', $guard, 'setRequest')
            $guard->setRequest(CHTTP::request());
        }

        return $guard;
    }

    /**
     * Create a token based authentication guard.
     *
     * @param string $name
     * @param array  $config
     *
     * @return CAuth_Guard_TokenGuard
     */
    public function createTokenDriver($name, $config) {
        // The token guard implements a basic API token based guard implementation
        // that takes an API token field from the request and matches it to the
        // user in the database or another persistence layer where users are.
        $guard = new CAuth_Guard_TokenGuard(
            $this->createUserProvider(carr::get($config, 'provider', null)),
            c::request(),
            carr::get($config, 'input_key', 'api_token'),
            carr::get($config, 'storage_key', 'api_token'),
            carr::get($config, 'hash', false)
        );

        c::container()->refresh('request', $guard, 'setRequest');

        return $guard;
    }

    /**
     * Get the guard configuration.
     *
     * @param string $name
     *
     * @return null|array
     */
    protected function getConfig($name) {
        return CF::config("auth.guards.{$name}");
    }

    /**
     * Get the default authentication driver name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        return CF::config('auth.defaults.guard');
    }

    /**
     * Set the default guard driver the factory should serve.
     *
     * @param string $name
     *
     * @return void
     */
    public function shouldUse($name) {
        $name = $name ?: $this->getDefaultDriver();

        $this->setDefaultDriver($name);

        $this->userResolver = function ($name = null) {
            return $this->guard($name)->user();
        };
    }

    /**
     * Set the default authentication driver name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name) {
        $this->defaultGuard = $name;
    }

    /**
     * Register a new callback based request guard.
     *
     * @param string   $driver
     * @param callable $callback
     *
     * @return $this
     */
    public function viaRequest($driver, callable $callback) {
        return $this->extend($driver, function () use ($callback) {
            $guard = new CAuth_Guard_RequestGuard($callback, CHTTP::request(), $this->createUserProvider());

            //c::container()->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Get the user resolver callback.
     *
     * @return \Closure
     */
    public function userResolver() {
        return $this->userResolver;
    }

    /**
     * Set the callback to be used to resolve users.
     *
     * @param \Closure $userResolver
     *
     * @return $this
     */
    public function resolveUsersUsing(Closure $userResolver) {
        $this->userResolver = $userResolver;

        return $this;
    }

    /**
     * Register a custom driver creator Closure.
     *
     * @param string   $driver
     * @param \Closure $callback
     *
     * @return $this
     */
    public function extend($driver, Closure $callback) {
        $this->customCreators[$driver] = $callback;

        return $this;
    }

    /**
     * Register a custom provider creator Closure.
     *
     * @param string   $name
     * @param \Closure $callback
     *
     * @return $this
     */
    public function provider($name, Closure $callback) {
        $this->customProviderCreators[$name] = $callback;

        return $this;
    }

    /**
     * Determines if any guards have already been resolved.
     *
     * @return bool
     */
    public function hasResolvedGuards() {
        return count($this->guards) > 0;
    }

    /**
     * Dynamically call the default driver instance.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call($method, $parameters) {
        return $this->guard()->{$method}(...$parameters);
    }
}

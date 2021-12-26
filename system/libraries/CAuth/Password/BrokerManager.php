<?php

class CAuth_Password_BrokerManager implements CAuth_Contract_PasswordBrokerFactoryInterface {
    /**
     * The array of created "drivers".
     *
     * @var array
     */
    protected $brokers = [];

    /**
     * The default password driver.
     */
    protected $defaultDriver = null;

    /**
     * Attempt to get the broker from the local cache.
     *
     * @param null|string $name
     *
     * @return \CAuth_Contract_PasswordBrokerInterface
     */
    public function broker($name = null) {
        $name = $name ?: $this->getDefaultDriver();

        return isset($this->brokers[$name]) ? $this->brokers[$name] : ($this->brokers[$name] = $this->resolve($name));
    }

    /**
     * Resolve the given broker.
     *
     * @param string $name
     *
     * @throws \InvalidArgumentException
     *
     * @return \CAuth_Contract_PasswordBrokerInterface
     */
    protected function resolve($name) {
        $config = $this->getConfig($name);

        if (is_null($config)) {
            throw new InvalidArgumentException("Password resetter [{$name}] is not defined.");
        }

        // The password broker uses a token repository to validate tokens and send user
        // password e-mails, as well as validating that password reset process as an
        // aggregate service of sorts providing a convenient interface for resets.
        return new CAuth_Password_Broker(
            $this->createTokenRepository($config),
            $this->app['auth']->createUserProvider($config['provider'] ?? null)
        );
    }

    /**
     * Create a token repository instance based on the given configuration.
     *
     * @param array $config
     *
     * @return \CAuth_Contract_TokenRepositoryInterface
     */
    protected function createTokenRepository(array $config) {
        $key = CF::config('app.key');

        if (cstr::startsWith($key, 'base64:')) {
            $key = base64_decode(substr($key, 7));
        }

        $connection = isset($config['connection']) ? $config['connection'] : null;

        return new CAuth_Password_DatabaseTokenRepository(
            CDatabase::instance($connection),
            $this->app['hash'],
            $config['table'],
            $key,
            $config['expire'],
            isset($config['throttle']) ? $config['throttle'] : 0
        );
    }

    /**
     * Get the password broker configuration.
     *
     * @param string $name
     *
     * @return array
     */
    protected function getConfig($name) {
        return CF::config("auth.passwords.{$name}");
    }

    /**
     * Get the default password broker name.
     *
     * @return string
     */
    public function getDefaultDriver() {
        if ($this->defaultDriver === null) {
            $this->defaultDriver = CF::config('auth.defaults.passwords');
        }

        return $this->defaultDriver;
    }

    /**
     * Set the default password broker name.
     *
     * @param string $name
     *
     * @return void
     */
    public function setDefaultDriver($name) {
        $this->defaultDriver = $name;

        return $this;
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
        return $this->broker()->{$method}(...$parameters);
    }
}

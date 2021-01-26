<?php

trait CAuth_Concern_CreateUserProvider {
    /**
     * The registered custom provider creators.
     *
     * @var array
     */
    protected $customProviderCreators = [];

    /**
     * Create the user provider implementation for the driver.
     *
     * @param string|null $provider
     *
     * @return CAuth_UserProviderInterface|null
     *
     * @throws \InvalidArgumentException
     */
    public function createUserProvider($provider = null) {
        if (is_null($config = $this->getProviderConfiguration($provider))) {
            return;
        }

        if (isset($this->customProviderCreators[$driver = (carr::get($config, 'driver', null))])) {
            return call_user_func(
                $this->customProviderCreators[$driver],
                $this->app,
                $config
            );
        }

        switch ($driver) {
            case 'database':
                return $this->createDatabaseProvider($config);
            case 'model':
                return $this->createModelProvider($config);
            default:
                throw new InvalidArgumentException(
                    "Authentication user provider [{$driver}] is not defined."
                );
        }
    }

    /**
     * Get the user provider configuration.
     *
     * @param string|null $provider
     *
     * @return array|null
     */
    protected function getProviderConfiguration($provider) {
        if ($provider = $provider ?: $this->getDefaultUserProvider()) {
            return CF::config('auth.providers.' . $provider);
        }
    }

    /**
     * Create an instance of the database user provider.
     *
     * @param array $config
     *
     * @return \CAuth_UserProvider_DatabaseUserProvider
     */
    protected function createDatabaseProvider($config) {
        $connection = $this->app['db']->connection(carr::get($config, 'connection', null));

        return new \CAuth_UserProvider_DatabaseUserProvider($connection, $this->app['hash'], $config['table']);
    }

    /**
     * Create an instance of the Model user provider.
     *
     * @param array $config
     *
     * @return \CAuth_UserProvider_ModelUserProvider
     */
    protected function createModelProvider($config) {
        return new \CAuth_UserProvider_ModelUserProvider(c::hash(carr::get($config, 'hasher')), $config['model']);
    }

    /**
     * Get the default user provider name.
     *
     * @return string
     */
    public function getDefaultUserProvider() {
        return CF::config('auth.defaults.provider');
    }
}

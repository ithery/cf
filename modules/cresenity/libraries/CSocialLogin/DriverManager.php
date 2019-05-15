<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 15, 2019, 8:13:37 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSocialLogin_DriverManager {

    use CTrait_Manager_DriverManager;

    protected $config;

    /**
     * Get a driver instance.
     *
     * @param  string  $driver
     * @return mixed
     */
    public function with($driver) {
        return $this->driver($driver);
    }

    public function setConfig($config) {
        $this->config = $config;
        return $this;
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGithubDriver() {
        $this->config;
        return $this->buildProvider(
                        GithubProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createFacebookDriver() {
        $config = $this->app['config']['services.facebook'];
        return $this->buildProvider(
                        FacebookProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGoogleDriver() {
        return $this->buildProvider(CSocialLogin_OAuth2_Provider_GoogleProvider::class, $this->config);
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createLinkedinDriver() {
        $config = $this->app['config']['services.linkedin'];
        return $this->buildProvider(
                        LinkedInProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createBitbucketDriver() {
        $config = $this->app['config']['services.bitbucket'];
        return $this->buildProvider(
                        BitbucketProvider::class, $config
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    protected function createGitlabDriver() {
        $config = $this->app['config']['services.gitlab'];
        return $this->buildProvider(
                        GitlabProvider::class, $config
        );
    }

    /**
     * Build an OAuth 2 provider instance.
     *
     * @param  string  $provider
     * @param  array  $config
     * @return \Laravel\Socialite\Two\AbstractProvider
     */
    public function buildProvider($provider, $config) {
        return new $provider($config['client_id'], $config['client_secret'], $this->formatRedirectUrl($config), carr::get($config, 'guzzle', [])
        );
    }

    /**
     * Create an instance of the specified driver.
     *
     * @return \Laravel\Socialite\One\AbstractProvider
     */
    protected function createTwitterDriver() {
        $config = $this->app['config']['services.twitter'];
        return new TwitterProvider(
                $this->app['request'], new TwitterServer($this->formatConfig($config))
        );
    }

    /**
     * Format the server configuration.
     *
     * @param  array  $config
     * @return array
     */
    public function formatConfig(array $config) {
        return array_merge([
            'identifier' => $config['client_id'],
            'secret' => $config['client_secret'],
            'callback_uri' => $this->formatRedirectUrl($config),
                ], $config);
    }

    /**
     * Format the callback URL, resolving a relative URI if needed.
     *
     * @param  array  $config
     * @return string
     */
    protected function formatRedirectUrl(array $config) {
        $redirect = CF::value($config['redirect']);
        return cstr::startsWith($redirect, '/') ? $this->app['url']->to($redirect) : $redirect;
    }

    /**
     * Get the default driver name.
     *
     * @throws \InvalidArgumentException
     *
     * @return string
     */
    public function getDefaultDriver() {
        throw new InvalidArgumentException('No Socialite driver was specified.');
    }

}

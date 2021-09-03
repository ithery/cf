<?php
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;

abstract class CTesting_BrowserTestCase extends CTesting_TestCase {
    use CTesting_Concern_ProvidesBrowser,
        CTesting_Chrome_SupportsChromeTrait;

    /**
     * Register the base URL with Dusk.
     *
     * @return void
     */
    protected function setUp(): void {
        parent::setUp();

        Browser::$baseUrl = $this->baseUrl();

        Browser::$storeScreenshotsAt = c::fixPath(CF::appDir()) . 'tests/Browser/screenshots';

        Browser::$storeConsoleLogAt = c::fixPath(CF::appDir()) . 'tests/Browser/console';

        Browser::$storeSourceAt = c::fixPath(CF::appDir()) . 'tests/Browser/source';

        Browser::$userResolver = function () {
            return $this->user();
        };
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver() {
        return RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()
        );
    }

    /**
     * Determine the application's base URL.
     *
     * @return string
     */
    protected function baseUrl() {
        return rtrim(CF::config('app.url'), '/');
    }

    /**
     * Return the default user to authenticate.
     *
     * @return \App\User|int|null
     *
     * @throws \Exception
     */
    protected function user() {
        throw new Exception('User resolver has not been set.');
    }

    /**
     * Determine if the tests are running within Laravel Sail.
     *
     * @return bool
     */
    protected static function runningInSail() {
        return isset($_ENV['LARAVEL_SAIL']) && $_ENV['LARAVEL_SAIL'] == '1';
    }
}

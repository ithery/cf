<?php
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\Remote\DesiredCapabilities;

abstract class CTesting_BrowserTestCase extends CTesting_TestCase {
    use CTesting_Concern_ProvidesBrowser;
    use CTesting_Chrome_SupportChromeTrait;

    /**
     * Register the base URL with Dusk.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();
        CTesting_Browser::$baseUrl = $this->baseUrl();
        CTesting_Browser::$storeScreenshotsAt = c::fixPath(CF::appDir()) . 'default/tests/Browser/screenshots';

        CTesting_Browser::$storeConsoleLogAt = c::fixPath(CF::appDir()) . 'default/tests/Browser/console';

        CTesting_Browser::$storeSourceAt = c::fixPath(CF::appDir()) . 'default/tests/Browser/source';

        if (!CFile::isDirectory(CTesting_Browser::$storeScreenshotsAt)) {
            CFile::makeDirectory(CTesting_Browser::$storeScreenshotsAt, 0755, true);
        }
        if (!CFile::isDirectory(CTesting_Browser::$storeConsoleLogAt)) {
            CFile::makeDirectory(CTesting_Browser::$storeConsoleLogAt, 0755, true);
        }
        if (!CFile::isDirectory(CTesting_Browser::$storeSourceAt)) {
            CFile::makeDirectory(CTesting_Browser::$storeSourceAt, 0755, true);
        }
        CTesting_Browser::$userResolver = function () {
            return $this->user();
        };
    }

    /**
     * Create the RemoteWebDriver instance.
     *
     * @return \Facebook\WebDriver\Remote\RemoteWebDriver
     */
    protected function driver() {
        $driver = RemoteWebDriver::create(
            'http://localhost:9515',
            DesiredCapabilities::chrome()
        );

        return $driver;
    }

    /**
     * Determine the application's base URL.
     *
     * @return string
     */
    protected function baseUrl() {
        return 'http://' . CF::domain();
    }

    /**
     * Return the default user to authenticate.
     *
     * @throws \Exception
     *
     * @return null|\CApp_Model_Users|int
     */
    protected function user() {
        throw new Exception('User resolver has not been set.');
    }
}

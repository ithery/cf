<?php

/**
 * Description of Configuration.
 *
 * @author Hery
 */
class CDevSuite_Windows_Configuration extends CDevSuite_Configuration {
    /**
     * Install the DevSuite configuration file.
     *
     * @return void
     */
    public function install() {
        $this->createConfigurationDirectory();
        $this->createDriversDirectory();
        $this->createSitesDirectory();
        $this->createExtensionsDirectory();
        $this->createLogDirectory();
        $this->createCertificatesDirectory();
        $this->createServicesDirectory();
        $this->createXdebugDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->path(), CDevSuite::user());
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory() {
        $this->files->ensureDirExists(preg_replace('~/devsuite$~', '', CDevSuite::homePath()), CDevSuite::user());

        $oldPath = $_SERVER['HOME'] . '/.devsuite';

        if ($this->files->isDir($oldPath)) {
            rename($oldPath, $this->devSuiteHomePath());
            $this->prependPath($this->devSuiteHomePath('Sites'));
        }

        $this->files->ensureDirExists(CDevSuite::homePath(), CDevSuite::user());
    }

    /**
     * Create the directory for the Windows services.
     *
     * @return void
     */
    public function createServicesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Services', CDevSuite::user());
    }

    /**
     * Create the directory for the Xdebug profiler.
     *
     * @return void
     */
    public function createXdebugDirectory() {
        $this->files->ensureDirExists($this->devSuiteHomePath('Xdebug'), CDevSuite::user());
    }

    /**
     * Write the base, initial configuration for Valet.
     *
     * @return void
     */
    public function writeBaseConfiguration() {
        if (!$this->files->exists($this->path())) {
            $this->write(['tld' => 'test', 'paths' => [], 'php_port' => CDevSuite_Windows_PhpCgi::PORT, 'php_xdebug_port' => CDevSuite_Windows_PhpCgiXdebug::PORT]);
        }

        $config = $this->read();

        // Migrate old configurations from 'domain' to 'tld'.
        if (!isset($config['tld'])) {
            $this->updateKey('tld', !empty($config['domain']) ? $config['domain'] : 'test');
        }

        // Add php_port if missing.
        $this->updateKey('php_port', $config['php_port'] ?? CDevSuite_Windows_PhpCgi::PORT);
        $this->updateKey('php_xdebug_port', $config['php_xdebug_port'] ?? CDevSuite_Windows_PhpCgiXdebug::PORT);
    }

    public function uninstall() {
        //do nothing on windows
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function path(): string {
        return $this->devSuiteHomePath('config.json');
    }

    /**
     * Get the Devsuite home path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function devSuiteHomePath(string $path = ''): string {
        return CDevSuite::homePath($path);
    }
}

<?php

/**
 * Description of Configuration
 *
 * @author Hery
 */
class CDevSuite_Mac_Configuration extends CDevSuite_Configuration {

    public $files;

    /**
     * Create a new DevSuite configuration class instance.
     */
    public function __construct() {
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Install the DevSuite configuration file.
     *
     * @return void
     */
    function install() {
        $this->createConfigurationDirectory();
        $this->createDriversDirectory();
        $this->createSitesDirectory();
        $this->createExtensionsDirectory();
        $this->createLogDirectory();
        $this->createCertificatesDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->path(), user());
    }

    /**
     * Forcefully delete the DevSuite home configuration directory and contents.
     * 
     * @return void
     */
    function uninstall() {
        $this->files->unlink(CDevSuite::homePath());
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    function createConfigurationDirectory() {
        $this->files->ensureDirExists(preg_replace('~/devsuite$~', '', CDevSuite::homePath()), user());

        $oldPath = posix_getpwuid(fileowner(__FILE__))['dir'] . '/.devsuite';

        if ($this->files->isDir($oldPath)) {
            rename($oldPath, CDevSuite::homePath());
            $this->prependPath(CDevSuite::homePath() . '/Sites');
        }

        $this->files->ensureDirExists(CDevSuite::homePath(), user());
    }

    /**
     * Create the DevSuite drivers directory.
     *
     * @return void
     */
    function createDriversDirectory() {
        if ($this->files->isDir($driversDirectory = CDevSuite::homePath() . '/Drivers')) {
            return;
        }

        $this->files->mkdirAsUser($driversDirectory);

        $this->files->putAsUser(
                $driversDirectory . '/SampleDevSuiteDriver.php', $this->files->get(CDevSuite::stubsPath(). 'mac/SampleDevSuiteDriver.php')
        );
    }

    /**
     * Create the DevSuite sites directory.
     *
     * @return void
     */
    function createSitesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Sites', user());
    }

    /**
     * Create the directory for the DevSuite extensions.
     *
     * @return void
     */
    function createExtensionsDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Extensions', user());
    }

    /**
     * Create the directory for Nginx logs.
     *
     * @return void
     */
    function createLogDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Log', user());

        $this->files->touch(CDevSuite::homePath() . '/Log/nginx-error.log');
    }

    /**
     * Create the directory for SSL certificates.
     *
     * @return void
     */
    function createCertificatesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Certificates', user());
    }

    /**
     * Write the base, initial configuration for DevSuite.
     */
    function writeBaseConfiguration() {
        if (!$this->files->exists($this->path())) {
            $this->write(['tld' => 'test', 'paths' => []]);
        }

        /**
         * Migrate old configurations from 'domain' to 'tld'
         */
        $config = $this->read();

        if (isset($config['tld'])) {
            return;
        }

        $this->updateKey('tld', !empty($config['domain']) ? $config['domain'] : 'test');
    }

    /**
     * Add the given path to the configuration.
     *
     * @param  string  $path
     * @param  bool  $prepend
     * @return void
     */
    function addPath($path, $prepend = false) {
        $this->write(tap($this->read(), function (&$config) use ($path, $prepend) {
                    $method = $prepend ? 'prepend' : 'push';

                    $config['paths'] = collect($config['paths'])->{$method}($path)->unique()->all();
                }));
    }

    /**
     * Prepend the given path to the configuration.
     *
     * @param  string  $path
     * @return void
     */
    function prependPath($path) {
        $this->addPath($path, true);
    }

    /**
     * Remove the given path from the configuration.
     *
     * @param  string  $path
     * @return void
     */
    function removePath($path) {
        if ($path == CDevSuite::homePath() . '/Sites') {
            info("Cannot remove this directory because this is where DevSuite stores its site definitions.\nRun [devsuite paths] for a list of parked paths.");
            die();
        }

        $this->write(tap($this->read(), function (&$config) use ($path) {
                    $config['paths'] = collect($config['paths'])->reject(function ($value) use ($path) {
                                return $value === $path;
                            })->values()->all();
                }));
    }

    /**
     * Prune all non-existent paths from the configuration.
     *
     * @return void
     */
    function prune() {
        if (!$this->files->exists($this->path())) {
            return;
        }

        $this->write(tap($this->read(), function (&$config) {
                    $config['paths'] = collect($config['paths'])->filter(function ($path) {
                                return $this->files->isDir($path);
                            })->values()->all();
                }));
    }

    /**
     * Read the configuration file as JSON.
     *
     * @return array
     */
    function read() {
        return json_decode($this->files->get($this->path()), true);
    }

    /**
     * Update a specific key in the configuration file.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return array
     */
    function updateKey($key, $value) {
        return tap($this->read(), function (&$config) use ($key, $value) {
            $config[$key] = $value;

            $this->write($config);
        });
    }

    /**
     * Write the given configuration to disk.
     *
     * @param  array  $config
     * @return void
     */
    function write($config) {
        $this->files->putAsUser($this->path(), json_encode(
                        $config, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
                ) . PHP_EOL);
    }

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    function path() {
        return CDevSuite::homePath() . '/config.json';
    }

}

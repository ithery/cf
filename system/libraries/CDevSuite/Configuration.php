<?php

abstract class CDevSuite_Configuration {
    /**
     * @var CDevSuite_Filesystem
     */
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
    public function install() {
        $this->createConfigurationDirectory();
        $this->createDriversDirectory();
        $this->createSitesDirectory();
        $this->createExtensionsDirectory();
        $this->createLogDirectory();
        $this->createCertificatesDirectory();
        $this->writeBaseConfiguration();

        $this->files->chown($this->path(), CDevSuite::user());
    }

    abstract public function uninstall();

    /**
     * Get the configuration file path.
     *
     * @return string
     */
    public function path() {
        return CDevSuite::homePath() . '/config.json';
    }

    /**
     * Write the given configuration to disk.
     *
     * @param array $config
     *
     * @return void
     */
    public function write($config) {
        $this->files->putAsUser($this->path(), json_encode(
            $config,
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        ) . PHP_EOL);
    }

    /**
     * Update a specific key in the configuration file.
     *
     * @param string $key
     * @param mixed  $value
     *
     * @return array
     */
    public function updateKey($key, $value) {
        return c::tap($this->read(), function (&$config) use ($key, $value) {
            $config[$key] = $value;
            $this->write($config);
        });
    }

    /**
     * Read the configuration file as JSON.
     *
     * @return array
     */
    public function read() {
        return json_decode($this->files->get($this->path()), true);
    }

    /**
     * Get a configuration value.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null) {
        $config = $this->read();

        return array_key_exists($key, $config) ? $config[$key] : $default;
    }

    /**
     * Prune all non-existent paths from the configuration.
     *
     * @return void
     */
    public function prune() {
        if (!$this->files->exists($this->path())) {
            return;
        }

        $this->write(c::tap($this->read(), function (&$config) {
            $config['paths'] = c::collect($config['paths'])->filter(function ($path) {
                return $this->files->isDir($path);
            })->values()->all();
        }));
    }

    /**
     * Remove the given path from the configuration.
     *
     * @param string $path
     *
     * @return void
     */
    public function removePath($path) {
        if ($path == CDevSuite::homePath() . '/Sites') {
            CDevSuite::info("Cannot remove this directory because this is where DevSuite stores its site definitions.\nRun [devsuite paths] for a list of parked paths.");
            die();
        }

        $this->write(c::tap($this->read(), function (&$config) use ($path) {
            $config['paths'] = c::collect($config['paths'])->reject(function ($value) use ($path) {
                return $value === $path;
            })->values()->all();
        }));
    }

    /**
     * Prepend the given path to the configuration.
     *
     * @param string $path
     *
     * @return void
     */
    public function prependPath($path) {
        $this->addPath($path, true);
    }

    /**
     * Add the given path to the configuration.
     *
     * @param string $path
     * @param bool   $prepend
     *
     * @return void
     */
    public function addPath($path, $prepend = false) {
        $this->write(c::tap($this->read(), function (&$config) use ($path, $prepend) {
            $method = $prepend ? 'prepend' : 'push';

            $config['paths'] = c::collect($config['paths'])->{$method}($path)->unique()->all();
        }));
    }

    /**
     * Write the base, initial configuration for DevSuite.
     */
    public function writeBaseConfiguration() {
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
     * Create the directory for SSL certificates.
     *
     * @return void
     */
    public function createCertificatesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Certificates', CDevSuite::user());
    }

    /**
     * Create the directory for Nginx logs.
     *
     * @return void
     */
    public function createLogDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Log', CDevSuite::user());

        $this->files->touch(CDevSuite::homePath() . '/Log/nginx-error.log');
    }

    /**
     * Create the directory for the DevSuite extensions.
     *
     * @return void
     */
    public function createExtensionsDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Extensions', CDevSuite::user());
    }

    /**
     * Create the DevSuite sites directory.
     *
     * @return void
     */
    public function createSitesDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath() . '/Sites', CDevSuite::user());
    }

    /**
     * Create the DevSuite drivers directory.
     *
     * @return void
     */
    public function createDriversDirectory() {
        $driversDirectory = CDevSuite::homePath() . '/Drivers';
        if ($this->files->isDir($driversDirectory)) {
            return;
        }

        $this->files->mkdirAsUser($driversDirectory);

        $this->files->putAsUser(
            $driversDirectory . '/SampleDevSuiteDriver.php',
            $this->files->get(CDevSuite::stubsPath() . 'SampleDevSuiteDriver.php')
        );
    }

    /**
     * Create the DevSuite configuration directory.
     *
     * @return void
     */
    public function createConfigurationDirectory() {
        $this->files->ensureDirExists(CDevSuite::homePath(), CDevSuite::user());
    }
}

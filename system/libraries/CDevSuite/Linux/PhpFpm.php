<?php

/**
 * Description of PhpFpm.
 *
 * @author Hery
 */
class CDevSuite_Linux_PhpFpm extends CDevSuite_PhpFpm {
    /**
     * @var CDevSuite_PackageManager
     */
    public $pm;

    public $sm;

    public $cli;

    public $files;

    public $version;

    /**
     * Create a new PHP FPM class instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();

        $this->pm = CDevSuite::packageManager();
        $this->sm = CDevSuite::serviceManager();

        $this->files = CDevSuite::filesystem();
        $this->version = $this->getVersion();
    }

    /**
     * Install and configure PHP FPM.
     *
     * @return void
     */
    public function install() {
        if (!$this->pm->installed("php{$this->version}-fpm")) {
            $this->pm->ensureInstalled("php{$this->version}-fpm");
            $this->sm->enable($this->fpmServiceName());
        }

        $this->files->ensureDirExists('/var/log', CDevSuite::user());

        $this->installConfiguration();

        $this->restart();
    }

    /**
     * Uninstall PHP FPM devsuite config.
     *
     * @return void
     */
    public function uninstall() {
        if ($this->files->exists('/etc/systemd/system/php-fpm.service.d/devsuite.conf')) {
            unlink('/etc/systemd/system/php-fpm.service.d/devsuite.conf');
        }

        if ($this->files->exists($this->fpmConfigPath() . '/devsuite.conf')) {
            $this->files->unlink($this->fpmConfigPath() . '/devsuite.conf');
            $this->stop();
        }
    }

    /**
     * Change the php-fpm version.
     *
     * @param string|float|int $version
     *
     * @return void
     */
    public function changeVersion($version = null) {
        $oldVersion = $this->version;
        $exception = null;

        $this->stop();
        CDevSuite::info('Disabling php' . $this->version . '-fpm...');
        $this->sm->disable($this->fpmServiceName());

        if (!isset($version) || strtolower($version) === 'default') {
            $this->version = $this->getVersion(true);
        } else {
            $this->version = $version;
        }

        try {
            $this->install();
        } catch (DomainException $e) {
            $this->version = $oldVersion;
            $exception = $e;
        }

        if ($this->sm->disabled($this->fpmServiceName())) {
            CDevSuite::info('Enabling php' . $this->version . '-fpm...');
            $this->sm->enable($this->fpmServiceName());
        }

        if ($this->version !== $this->getVersion(true)) {
            $this->files->putAsUser(CDevSuite::homePath() . 'use_php_version', $this->version);
        } else {
            $this->files->unlink(CDevSuite::homePath() . 'use_php_version');
        }

        if ($exception) {
            CDevSuite::info('Changing version failed');

            throw $exception;
        }

        $this->updateCliVersion();
    }

    /**
     * Update the PHP CLI version.
     *
     * @return void
     */
    protected function updateCliVersion() {
        $path = $this->cli->run("which php{$this->version}");

        $this->cli->run("update-alternatives --set php $path");
    }

    /**
     * Update the PHP FPM configuration to use the current user.
     *
     * @return void
     */
    public function installConfiguration() {
        $contents = $this->files->get(CDevSuite::stubsPath() . 'fpm.conf');

        $contents = str_replace(
            ['DEVSUITE_USER', 'DEVSUITE_HOME_PATH'],
            [CDevSuite::user(), rtrim(CDevSuite::homePath(), '/')],
            $contents
        );

        $this->files->putAsRoot(
            $this->fpmConfigPath() . '/devsuite.conf',
            str_replace(
                ['DEVSUITE_USER', 'DEVSUITE_GROUP', 'DEVSUITE_HOME_PATH'],
                [CDevSuite::user(), CDevSuite::group(), rtrim(CDevSuite::homePath(), '/')],
                $contents
            )
        );

        if (($this->sm) instanceof CDevSuite_ServiceManager_Systemd) {
            $this->systemdDropInOverride();
        }
    }

    /**
     * Install Drop-In systemd override for php-fpm service.
     *
     * @return void
     */
    public function systemdDropInOverride() {
        $this->files->ensureDirExists('/etc/systemd/system/php-fpm.service.d');
        $this->files->putAsUser(
            '/etc/systemd/system/php-fpm.service.d/devsuite.conf',
            $this->files->get(CDevSuite::homePath() . 'php-fpm.service.d/devsuite.conf')
        );
    }

    /**
     * Restart the PHP FPM process.
     *
     * @return void
     */
    public function restart() {
        $this->sm->restart($this->fpmServiceName());
    }

    /**
     * Stop the PHP FPM process.
     *
     * @return void
     */
    public function stop() {
        $this->sm->stop($this->fpmServiceName());
    }

    /**
     * PHP-FPM service status.
     *
     * @return void
     */
    public function status() {
        $this->sm->printStatus($this->fpmServiceName());
    }

    /**
     * Get installed PHP version.
     *
     * @param string $real force getting version from /usr/bin/php
     *
     * @return string
     */
    public function getVersion($real = false) {
        if (!$real && $this->files->exists(CDevSuite::homePath() . '/use_php_version')) {
            $version = $this->files->get(CDevSuite::homePath() . '/use_php_version');
        } else {
            $version = explode('php', basename($this->files->readLink('/usr/bin/php')))[1];
        }

        return $version;
    }

    /**
     * Determine php service name.
     *
     * @return string
     */
    public function fpmServiceName() {
        $service = 'php' . $this->version . '-fpm';
        $status = $this->sm->status($service);

        if (strpos($status, 'not-found') || strpos($status, 'not be found')) {
            return new DomainException('Unable to determine PHP service name.');
        }

        return $service;
    }

    /**
     * Get the path to the FPM configuration file for the current PHP version.
     *
     * @return string
     */
    public function fpmConfigPath() {
        return c::collect([
            '/etc/php/' . $this->version . '/fpm/pool.d', // Ubuntu
            '/etc/php' . $this->version . '/fpm/pool.d', // Ubuntu
            '/etc/php-fpm.d', // Fedora
            '/etc/php/php-fpm.d', // Arch
            '/etc/php7/fpm/php-fpm.d', // openSUSE
        ])->first(function ($path) {
            return is_dir($path);
        }, function () {
            throw new DomainException('Unable to determine PHP-FPM configuration folder.');
        });
    }
}

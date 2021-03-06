<?php

/**
 * Description of PhpFpm.
 *
 * @author Hery
 */
class CDevSuite_Mac_PhpFpm extends CDevSuite_PhpFpm {
    public $cli;

    public $files;

    public $brew;

    public $taps = [
        'homebrew/homebrew-core'
    ];

    /**
     * Create a new PHP FPM class instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->brew = CDevSuite::brew();
    }

    /**
     * Install and configure PhpFpm.
     *
     * @return void
     */
    public function install() {
        if (!$this->brew->hasInstalledPhp()) {
            $this->brew->ensureInstalled('php', [], $this->taps);
        }

        $this->files->ensureDirExists('/usr/local/var/log', CDevSuite::user());

        $this->updateConfiguration();

        $this->restart();
    }

    /**
     * Forcefully uninstall all of DevSuite's supported PHP versions and configurations.
     *
     * @return void
     */
    public function uninstall() {
        $this->brew->uninstallAllPhpVersions();
        rename(BREW_PREFIX . '/etc/php', BREW_PREFIX . '/etc/php-devsuite-bak' . time());
        $this->cli->run('rm -rf ' . BREW_PREFIX . '/var/log/php-fpm.log');
    }

    /**
     * Update the PHP FPM configuration.
     *
     * @return void
     */
    public function updateConfiguration() {
        CDevSuite::info('Updating PHP configuration...');

        $fpmConfigFile = $this->fpmConfigPath();

        $this->files->ensureDirExists(dirname($fpmConfigFile), CDevSuite::user());

        // rename (to disable) old FPM Pool configuration, regardless of whether it's a default config or one customized by an older DevSuite version
        $oldFile = dirname($fpmConfigFile) . '/www.conf';
        if (file_exists($oldFile)) {
            rename($oldFile, $oldFile . '-backup');
        }

        if (false === strpos($fpmConfigFile, '5.6')) {
            // for PHP 7 we can simply drop in a devsuite-specific fpm pool config, and not touch the default config
            $contents = $this->files->get(CDevSuite::stubsPath() . 'etc-phpfpm-devsuite.conf');
            $contents = str_replace(['DEVSUITE_USER', 'DEVSUITE_HOME_PATH'], [CDevSuite::user(), rtrim(CDevSuite::homePath(), '/')], $contents);
        } else {
            // for PHP 5 we must do a direct edit of the fpm pool config to switch it to DevSuite's needs
            $contents = $this->files->get($fpmConfigFile);
            $contents = preg_replace('/^user = .+$/m', 'user = ' . CDevSuite::user(), $contents);
            $contents = preg_replace('/^group = .+$/m', 'group = staff', $contents);
            $contents = preg_replace('/^listen = .+$/m', 'listen = ' . CDevSuite::homePath() . '/devsuite.sock', $contents);
            $contents = preg_replace('/^;?listen\.owner = .+$/m', 'listen.owner = ' . CDevSuite::user(), $contents);
            $contents = preg_replace('/^;?listen\.group = .+$/m', 'listen.group = staff', $contents);
            $contents = preg_replace('/^;?listen\.mode = .+$/m', 'listen.mode = 0777', $contents);
        }

        CDevSuite::info($fpmConfigFile);
        $this->files->put($fpmConfigFile, $contents);

        $contents = $this->files->get(CDevSuite::stubsPath() . 'php-memory-limits.ini');

        $destFile = dirname($fpmConfigFile);
        $destFile = str_replace('/php-fpm.d', '', $destFile);
        $destFile .= '/conf.d/php-memory-limits.ini';
        $this->files->ensureDirExists(dirname($destFile), CDevSuite::user());

        $this->files->putAsUser($destFile, $contents);
    }

    /**
     * Restart the PHP FPM process.
     *
     * @return void
     */
    public function restart() {
        $this->brew->restartLinkedPhp();
    }

    /**
     * Stop the PHP FPM process.
     *
     * @return void
     */
    public function stop() {
        call_user_func_array(
            [$this->brew, 'stopService'],
            CDevSuite_Brew::SUPPORTED_PHP_VERSIONS
        );
    }

    /**
     * Get the path to the FPM configuration file for the current PHP version.
     *
     * @return string
     */
    public function fpmConfigPath() {
        $version = $this->brew->linkedPhp();

        $versionNormalized = preg_replace(
            '/php@?(\d)\.?(\d)/',
            '$1.$2',
            $version === 'php' ? CDevSuite_Brew::LATEST_PHP_VERSION : $version
        );

        return $versionNormalized === '5.6' ? BREW_PREFIX . '/etc/php/5.6/php-fpm.conf' : BREW_PREFIX . "/etc/php/${versionNormalized}/php-fpm.d/devsuite-fpm.conf";
    }

    /**
     * Only stop running php services.
     */
    public function stopRunning() {
        $this->brew->stopService(
            $this->brew->getRunningServices()
                ->filter(function ($service) {
                    return substr($service, 0, 3) === 'php';
                })
                ->all()
        );
    }

    /**
     * Use a specific version of php.
     *
     * @param $version
     *
     * @return string
     */
    public function useVersion($version) {
        $version = $this->validateRequestedVersion($version);

        // Install the relevant formula if not already installed
        $this->brew->ensureInstalled($version);

        // Unlink the current php if there is one
        if ($this->brew->hasLinkedPhp()) {
            $currentVersion = $this->brew->getLinkedPhpFormula();
            CDevSuite::info(sprintf('Unlinking current version: %s', $currentVersion));
            $this->brew->unlink($currentVersion);
        }

        CDevSuite::info(sprintf('Linking new version: %s', $version));
        $this->brew->link($version, true);

        $this->install();

        return $version === 'php' ? $this->brew->determineAliasedVersion($version) : $version;
    }

    /**
     * Validate the requested version to be sure we can support it.
     *
     * @param $version
     *
     * @return string
     */
    public function validateRequestedVersion($version) {
        // If passed php7.2 or php72 formats, normalize to php@7.2 format:
        $version = preg_replace('/(php)([0-9+])(?:.)?([0-9+])/i', '$1@$2.$3', $version);

        if ($version === 'php') {
            if (strpos($this->brew->determineAliasedVersion($version), '@')) {
                return $version;
            }

            if ($this->brew->hasInstalledPhp()) {
                throw new DomainException('Brew is already using PHP ' . PHP_VERSION . ' as \'php\' in Homebrew. To use another version, please specify. eg: php@7.3');
            }
        }

        if (!$this->brew->supportedPhpVersions()->contains($version)) {
            throw new DomainException(
                sprintf(
                    'DevSuite doesn\'t support PHP version: %s (try something like \'php@7.3\' instead)',
                    $version
                )
            );
        }

        return $version;
    }
}

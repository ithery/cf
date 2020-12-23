<?php

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 *
 * @since Nov 15, 2020
 *
 * @license Ittron Global Teknologi
 */
class CDevSuite_Brew {
    const SUPPORTED_PHP_VERSIONS = [
        'php',
        'php@8.1',
        'php@8.0',
        'php@7.4',
        'php@7.3',
        'php@7.2',
        'php@7.1',
        'php@7.0',
        'php@5.6',
        'php73',
        'php72',
        'php71',
        'php70',
        'php56'
    ];
    const LATEST_PHP_VERSION = 'php@7.4';

    public $cli;
    public $files;

    /**
     * Create a new Brew instance.
     *
     * @param CommandLine $cli
     * @param Filesystem  $files
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Determine if the given formula is installed.
     *
     * @param string $formula
     *
     * @return bool
     */
    public function installed($formula) {
        return in_array($formula, explode(PHP_EOL, $this->cli->runAsUser('brew list --formula | grep ' . $formula)));
    }

    /**
     * Determine if a compatible PHP version is Homebrewed.
     *
     * @return bool
     */
    public function hasInstalledPhp() {
        return $this->supportedPhpVersions()->contains(function ($version) {
            return $this->installed($version);
        });
    }

    /**
     * Get a list of supported PHP versions.
     *
     * @return \Illuminate\Support\Collection
     */
    public function supportedPhpVersions() {
        return c::collect(static::SUPPORTED_PHP_VERSIONS);
    }

    /**
     * Get the aliased formula version from Homebrew
     *
     * @param mixed $formula
     */
    public function determineAliasedVersion($formula) {
        $details = json_decode($this->cli->runAsUser("brew info $formula --json"));
        return $details[0]->aliases[0] ?: 'ERROR - NO BREW ALIAS FOUND';
    }

    /**
     * Determine if a compatible nginx version is Homebrewed.
     *
     * @return bool
     */
    public function hasInstalledNginx() {
        return $this->installed('nginx') || $this->installed('nginx-full');
    }

    /**
     * Determine if a compatible nginx version is Homebrewed.
     *
     * @return bool
     */
    public function hasInstalledMariaDb() {
        return $this->installed('mariadb');
    }

    /**
     * Return name of the nginx service installed via Homebrew.
     *
     * @return string
     */
    public function nginxServiceName() {
        return $this->installed('nginx-full') ? 'nginx-full' : 'nginx';
    }

    /**
     * Ensure that the given formula is installed.
     *
     * @param string $formula
     * @param array  $options
     * @param array  $taps
     *
     * @return void
     */
    public function ensureInstalled($formula, $options = [], $taps = []) {
        if (!$this->installed($formula)) {
            $this->installOrFail($formula, $options, $taps);
        }
    }

    /**
     * Install the given formula and throw an exception on failure.
     *
     * @param string $formula
     * @param array  $options
     * @param array  $taps
     *
     * @return void
     */
    public function installOrFail($formula, $options = [], $taps = []) {
        CDevSuite::info("Installing {$formula}...");

        if (count($taps) > 0) {
            $this->tap($taps);
        }

        CDevSuite::output('<info>[' . $formula . '] is not installed, installing it now via Brew...</info> 🍻');

        $this->cli->runAsUser(trim('brew install ' . $formula . ' ' . implode(' ', $options)), function ($exitCode, $errorOutput) use ($formula) {
            CDevSuite::output($errorOutput);

            throw new DomainException('Brew was unable to install [' . $formula . '].');
        });
    }

    /**
     * Tap the given formulas.
     *
     * @param dynamic[string] $formula
     * @param mixed           $formulas
     *
     * @return void
     */
    public function tap($formulas) {
        $formulas = is_array($formulas) ? $formulas : func_get_args();

        foreach ($formulas as $formula) {
            $this->cli->passthru('sudo -u "' . CDevSuite::user() . '" brew tap ' . $formula);
        }
    }

    /**
     * Restart the given Homebrew services.
     *
     * @param
     * @param mixed $services
     */
    public function restartService($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            if ($this->installed($service)) {
                CDevSuite::info("Restarting {$service}...");

                $this->cli->quietly('sudo brew services stop ' . $service);
                $this->cli->quietly('sudo brew services start ' . $service);
            }
        }
    }

    /**
     * Stop the given Homebrew services.
     *
     * @param
     * @param mixed $services
     */
    public function stopService($services) {
        $services = is_array($services) ? $services : func_get_args();

        foreach ($services as $service) {
            if ($this->installed($service)) {
                CDevSuite::info("Stopping {$service}...");

                $this->cli->quietly('sudo brew services stop ' . $service);
            }
        }
    }

    /**
     * Determine if php is currently linked.
     *
     * @return bool
     */
    public function hasLinkedPhp() {
        return $this->files->isLink('/usr/local/bin/php');
    }

    /**
     * Get the linked php parsed.
     *
     * @return mixed
     */
    public function getParsedLinkedPhp() {
        if (!$this->hasLinkedPhp()) {
            throw new DomainException('Homebrew PHP appears not to be linked.');
        }

        $resolvedPath = $this->files->readLink('/usr/local/bin/php');

        /**
         * Typical homebrew path resolutions are like:
         * "../Cellar/php@7.2/7.2.13/bin/php"
         * or older styles:
         * "../Cellar/php/7.2.9_2/bin/php
         * "../Cellar/php55/bin/php
         */
        preg_match('~\w{3,}/(php)(@?\d\.?\d)?/(\d\.\d)?([_\d\.]*)?/?\w{3,}~', $resolvedPath, $matches);

        return $matches;
    }

    /**
     * Gets the currently linked formula
     * E.g if under php, will be php, if under php@7.3 will be that
     * Different to ->linkedPhp() in that this will just get the linked directory name (whether that is php, php55 or
     * php@7.2)
     *
     * @return mixed
     */
    public function getLinkedPhpFormula() {
        $matches = $this->getParsedLinkedPhp();
        return $matches[1] . $matches[2];
    }

    /**
     * Determine which version of PHP is linked in Homebrew.
     *
     * @return string
     */
    public function linkedPhp() {
        $matches = $this->getParsedLinkedPhp();
        $resolvedPhpVersion = $matches[3] ?: $matches[2];

        return $this->supportedPhpVersions()->first(
            function ($version) use ($resolvedPhpVersion) {
                $resolvedVersionNormalized = preg_replace('/[^\d]/', '', $resolvedPhpVersion);
                $versionNormalized = preg_replace('/[^\d]/', '', $version);
                return $resolvedVersionNormalized === $versionNormalized;
            },
            function () use ($resolvedPhpVersion) {
                throw new DomainException("Unable to determine linked PHP when parsing '$resolvedPhpVersion'");
            }
        );
    }

    /**
     * Restart the linked PHP-FPM Homebrew service.
     *
     * @return void
     */
    public function restartLinkedPhp() {
        CDevSuite::info($this->getLinkedPhpFormula());
        $this->restartService($this->getLinkedPhpFormula());
    }

    /**
     * Create the "sudoers.d" entry for running Brew.
     *
     * @return void
     */
    public function createSudoersEntry() {
        $this->files->ensureDirExists('/etc/sudoers.d');

        $this->files->put('/etc/sudoers.d/brew', 'Cmnd_Alias BREW = /usr/local/bin/brew *
%admin ALL=(root) NOPASSWD:SETENV: BREW' . PHP_EOL);
    }

    /**
     * Remove the "sudoers.d" entry for running Brew.
     *
     * @return void
     */
    public function removeSudoersEntry() {
        $this->cli->quietly('rm /etc/sudoers.d/brew');
    }

    /**
     * Link passed formula.
     *
     * @param $formula
     * @param bool $force
     *
     * @return string
     */
    public function link($formula, $force = false) {
        return $this->cli->runAsUser(
            sprintf('brew link %s%s', $formula, $force ? ' --force' : ''),
            function ($exitCode, $errorOutput) use ($formula) {
                CDevSuite::output($errorOutput);

                throw new DomainException('Brew was unable to link [' . $formula . '].');
            }
        );
    }

    /**
     * Unlink passed formula.
     *
     * @param $formula
     *
     * @return string
     */
    public function unlink($formula) {
        return $this->cli->runAsUser(
            sprintf('brew unlink %s', $formula),
            function ($exitCode, $errorOutput) use ($formula) {
                CDevSuite::output($errorOutput);

                throw new DomainException('Brew was unable to unlink [' . $formula . '].');
            }
        );
    }

    /**
     * Get the currently running brew services.
     *
     * @return \Illuminate\Support\Collection
     */
    public function getRunningServices() {
        return c::collect(array_filter(explode(PHP_EOL, $this->cli->runAsUser(
            'brew services list | grep started | awk \'{ print $1; }\'',
            function ($exitCode, $errorOutput) {
                CDevSuite::output($errorOutput);

                throw new DomainException('Brew was unable to check which services are running.');
            }
        ))));
    }

    /**
     * Tell Homebrew to forcefully remove all PHP versions that DevSuite supports.
     *
     * @return string
     */
    public function uninstallAllPhpVersions() {
        $this->supportedPhpVersions()->each(function ($formula) {
            $this->uninstallFormula($formula);
        });

        return 'PHP versions removed.';
    }

    /**
     * Uninstall a Homebrew app by formula name.
     *
     * @param string $formula
     *
     * @return void
     */
    public function uninstallFormula($formula) {
        $this->cli->runAsUser('brew uninstall --force ' . $formula);
        $this->cli->run('rm -rf /usr/local/Cellar/' . $formula);
    }

    /**
     * Run Homebrew's cleanup commands.
     *
     * @return string
     */
    public function cleanupBrew() {
        return $this->cli->runAsUser(
            'brew cleanup && brew services cleanup',
            function ($exitCode, $errorOutput) {
                CDevSuite::output($errorOutput);
            }
        );
    }
}

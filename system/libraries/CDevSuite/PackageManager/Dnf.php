<?php

class CDevSuite_PackageManager_Dnf extends CDevSuite_PackageManager {
    const SUPPORTED_PHP_VERSIONS = [
        'php',
    ];

    public $cli;

    /**
     * Create a new Apt instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
    }

    /**
     * Determine if the given package is installed.
     *
     * @param string $package
     *
     * @return bool
     */
    public function installed($package) {
        $query = "dnf list installed {$package} | grep {$package} | sed 's_  _\\t_g' | sed 's_\\._\\t_g' | cut -f 1";

        $packages = explode(PHP_EOL, $this->cli->run($query));

        return in_array($package, $packages);
    }

    /**
     * Ensure that the given package is installed.
     *
     * @param string $package
     *
     * @return void
     */
    public function ensureInstalled($package) {
        if (!$this->installed($package)) {
            $this->installOrFail($package);
        }
    }

    /**
     * Install the given package and throw an exception on failure.
     *
     * @param string $package
     *
     * @return void
     */
    public function installOrFail($package) {
        CDevsuite::output('<info>[' . $package . '] is not installed, installing it now via Dnf...</info> 🍻');

        $this->cli->run(trim('dnf install -y ' . $package), function ($exitCode, $errorOutput) use ($package) {
            CDevsuite::output($errorOutput);

            throw new DomainException('Dnf was unable to install [' . $package . '].');
        });
    }

    /**
     * Configure package manager on devsuite install.
     *
     * @return void
     */
    public function setup() {
        // Nothing to do
    }

    /**
     * Restart dnsmasq in Fedora.
     *
     * @param mixed $sm
     */
    public function nmRestart($sm) {
        $sm->restart('NetworkManager');
    }

    /**
     * Determine if package manager is available on the system.
     *
     * @return bool
     */
    public function isAvailable() {
        try {
            $output = $this->cli->run('which dnf', function ($exitCode, $output) {
                throw new DomainException('Dnf not available');
            });

            return $output != '';
        } catch (DomainException $e) {
            return false;
        }
    }

    public function supportedPhpVersions() {
        return c::collect(static::SUPPORTED_PHP_VERSIONS);
    }
}

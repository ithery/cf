<?php

class CDevSuite_PackageManager_Apt extends CDevSuite_PackageManager {
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
     * Get array of installed packages.
     *
     * @param string $package
     *
     * @return array
     */
    public function packages($package) {
        $query = "dpkg -l {$package} | grep '^ii' | sed 's/\s\+/ /g' | cut -d' ' -f2";

        return explode(PHP_EOL, $this->cli->run($query));
    }

    /**
     * Determine if the given package is installed.
     *
     * @param string $package
     *
     * @return bool
     */
    public function installed($package) {
        return in_array($package, $this->packages($package));
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
        CDevSuite::output('<info>[' . $package . '] is not installed, installing it now via Apt...</info> 🍻');

        $this->cli->run(trim('apt install -y ' . $package), function ($exitCode, $errorOutput) use ($package) {
            CDevSuite::output($errorOutput);

            throw new DomainException('Apt was unable to install [' . $package . '].');
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
     * Restart dnsmasq in Ubuntu.
     *
     * @param mixed $sm
     */
    public function nmRestart($sm) {
        $sm->restart(['network-manager']);
    }

    /**
     * Determine if package manager is available on the system.
     *
     * @return bool
     */
    public function isAvailable() {
        try {
            $output = $this->cli->run('which apt', function ($exitCode, $output) {
                throw new DomainException('Apt not available');
            });

            return $output != '';
        } catch (DomainException $e) {
            return false;
        }
    }
}

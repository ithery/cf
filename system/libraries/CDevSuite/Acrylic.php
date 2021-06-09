<?php

/**
 * Description of Acrylic
 *
 * @author Hery
 */
class CDevSuite_Acrylic {
    protected $cli;
    protected $files;

    /**
     * Create a new Acrylic instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Install the Acrylic DNS service.
     *
     * @param string $tld
     *
     * @return void
     */
    public function install($tld = 'test') {
        $this->uninstall();
        $this->createHostsFile($tld);

        $this->configureNetworkDNS();

        $this->cli->runOrDie('cmd /C "' . $this->path() . '/AcrylicUI.exe" InstallAcrylicService', function ($code, $output) {
            CDevSuite::warning($output);
        });

        $this->restart();
    }

    /**
     * Create the AcrylicHosts file.
     *
     * @param string $tld
     *
     * @return void
     */
    public function createHostsFile($tld) {
        $contents = $this->files->get(CDevSuite::stubsPath() . 'AcrylicHosts.txt');

        $this->files->put(
            $this->path() . '/AcrylicHosts.txt',
            str_replace(['DEVSUITE_TLD', 'DEVSUITE_HOME_PATH'], [$tld, rtrim(CDevSuite::homePath(), '/')], $contents)
        );

        $customConfigPath = CDevSuite::homePath() . '/AcrylicHosts.txt';

        if (!$this->files->exists($customConfigPath)) {
            $this->files->putAsUser($customConfigPath, PHP_EOL);
        }
    }

    /**
     * Configure the Network DNS.
     *
     * @return void
     */
    public function configureNetworkDNS() {
        $bin = realpath(CDevSuite::binPath());

        $this->cli->run('cmd /C cd "' . $bin . '" && configuredns');
    }

    /**
     * Update the tld used by Acrylic DNS.
     *
     * @param string $tld
     *
     * @return void
     */
    public function updateTld($tld) {
        $this->stop();

        $this->createHostsFile($tld);

        $this->restart();
    }

    /**
     * Uninstall the Acrylic DNS service.
     *
     * @return void
     */
    public function uninstall() {
        $this->stop();

        $this->cli->run('cmd /C "' . $this->path() . '/AcrylicUI.exe" UninstallAcrylicService');
    }

    /**
     * Start the Acrylic DNS service.
     *
     * @return void
     */
    public function start() {
        $this->cli->runOrDie('cmd /C "' . $this->path() . '/AcrylicUI.exe" StartAcrylicService', function ($code, $output) {
            CDevSuite::warning($output);
        });

        $this->flushdns();
    }

    /**
     * Stop the Acrylic DNS service.
     *
     * @return void
     */
    public function stop() {
        $this->cli->run('cmd /C "' . $this->path() . '/AcrylicUI.exe" StopAcrylicService');

        $this->flushdns();
    }

    /**
     * Restart the Acrylic DNS service.
     *
     * @return void
     */
    public function restart() {
        $this->stop();

        $this->start();
    }

    /**
     * Flush Windows DNS.
     *
     * @return void
     */
    public function flushdns() {
        $this->cli->run('cmd "/C ipconfig /flushdns"');
    }

    /**
     * Get the Acrylic path.
     *
     * @return string
     */
    public function path() {
        return str_replace(DIRECTORY_SEPARATOR, '/', realpath(CDevSuite::binPath() . 'acrylic/'));
    }
}

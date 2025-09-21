<?php

class CDevSuite_Windows_WinSW {
    /**
     * @var string
     */
    protected $service;

    /**
     * @var CDevSuite_Windows_CommandLine
     */
    protected $cli;

    /**
     * @var CDevSuite_Windows_Filesystem
     */
    protected $files;

    /**
     * Create a new WinSW instance.
     *
     * @return void
     */
    public function __construct(string $service) {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->service = $service;
    }

    /**
     * Install the service.
     *
     * @param array $args
     *
     * @return void
     */
    public function install(array $args = []) {
        $this->createConfiguration($args);

        $command = 'cmd "/C cd ' . $this->servicesPath() . ' && "' . $this->servicesPath($this->service) . '" install"';

        $this->cli->runOrExit($command, function ($code, $output) {
            CDevSuite::error("Failed to install service [$this->service]. Check ~/.config/devsuite/Log for errors.\n$output");
        });
    }

    /**
     * Create the .exe and .xml files.
     *
     * @param array $args
     *
     * @return void
     */
    protected function createConfiguration(array $args = []) {
        $args['DEVSUITE_HOME_PATH'] = CDevSuite::homePath();

        $bin = realpath(CDevSuite::binPath());
        $this->files->copy(
            $bin . '/winsw/WinSW.NET4.exe',
            $this->binaryPath()
        );
        $contents = $this->files->get(CDevSuite::stubsPath() . $this->service . '.xml');

        $this->files->put(
            $this->configPath(),
            str_replace(array_keys($args), array_values($args), $contents ?: '')
        );
    }

    /**
     * Uninstall the service.
     *
     * @return void
     */
    public function uninstall() {
        $this->stop($this->service);

        $this->cli->run('cmd "/C cd ' . $this->servicesPath() . ' && "' . $this->servicesPath($this->service) . '" uninstall"');

        sleep(1);

        $this->files->unlink($this->binaryPath());
        $this->files->unlink($this->configPath());
    }

    /**
     * Determine if the service is installed.
     *
     * @return bool
     */
    public function installed(): bool {
        $name = 'devsuite_' . str_replace('service', '', $this->service);

        if ($name === 'devsuite_phpcgixdebug') {
            $name = 'devsuite_phpcgi_xdebug';
        }

        return $this->cli->powershell("Get-Service -Name \"$name\"")->isSuccessful();
    }

    /**
     * Restart the service.
     *
     * @return void
     */
    public function restart() {
        $command = 'cmd "/C cd ' . $this->servicesPath() . ' && "' . $this->servicesPath($this->service) . '" restart"';

        $this->cli->run($command, function () use ($command) {
            sleep(2);

            $this->cli->runOrExit($command, function ($code, $output) {
                error("Failed to restart service [$this->service]. Check ~/.config/devsuite/Log for errors.\n$output");
            });
        });
    }

    /**
     * Stop the service.
     *
     * @return void
     */
    public function stop() {
        $command = 'cmd "/C cd ' . $this->servicesPath() . ' && "' . $this->servicesPath($this->service) . '" stop"';

        $this->cli->run($command, function ($code, $output) {
            warning("Failed to stop service [$this->service].\n$output");
        });
    }

    /**
     * Get the config path.
     *
     * @return string
     */
    protected function configPath(): string {
        return $this->servicesPath("$this->service.xml");
    }

    /**
     * Get the binary path.
     *
     * @return string
     */
    protected function binaryPath(): string {
        return $this->servicesPath("$this->service.exe");
    }

    /**
     * Get the services path.
     *
     * @param string $path
     *
     * @return string
     */
    protected function servicesPath(string $path = ''): string {
        return CDevSuite::homePath('Services' . ($path ? DIRECTORY_SEPARATOR . $path : $path));
    }
}

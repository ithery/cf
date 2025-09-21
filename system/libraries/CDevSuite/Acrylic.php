<?php

/**
 * Description of Acrylic.
 *
 * @author Hery
 */
class CDevSuite_Acrylic {
    /**
     * @var CDevSuite_Windows_CommandLine
     */
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
        // $this->uninstall();
        CDevSuite::info('Installing Acrylic DNS...');
        $this->createHostsFile($tld);
        $this->installService();
        // $this->configureNetworkDNS();

        // $this->cli->runOrDie('cmd /C "' . $this->path() . '/AcrylicUI.exe" InstallAcrylicService', function ($code, $output) {
        //     CDevSuite::warning($output);
        // });

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
            $this->path('AcrylicHosts.txt'),
            str_replace(['DEVSUITE_TLD', 'DEVSUITE_HOME_PATH'], [$tld, rtrim(CDevSuite::homePath(), '/')], $contents)
        );

        $customConfigPath = CDevSuite::homePath() . '/AcrylicHosts.txt';

        if (!$this->files->exists($customConfigPath)) {
            $this->files->putAsUser($customConfigPath, PHP_EOL);
        }
    }

    /**
     * Install the Acrylic DNS service.
     *
     * @return void
     */
    protected function installService() {
        $this->uninstall();

        $this->configureNetworkDNS();

        $this->cli->runOrExit('cmd /C "' . $this->path('AcrylicUI.exe') . '" InstallAcrylicService', function ($code, $output) {
            error("Failed to install Acrylic DNS: $output");
        });

        $this->flushdns();
    }

    /**
     * Configure the Network DNS.
     *
     * @return void
     */
    public function configureNetworkDNS() {
        // $bin = realpath(CDevSuite::binPath());

        // $this->cli->run('cmd /C cd "' . $bin . '" && configuredns');

        $this->cli->powershell(implode(';', [
            '(Get-NetIPAddress -AddressFamily IPv4).InterfaceIndex | ForEach-Object {Set-DnsClientServerAddress -InterfaceIndex $_ -ServerAddresses (\"127.0.0.1\", \"8.8.8.8\")}',
            '(Get-NetIPAddress -AddressFamily IPv6).InterfaceIndex | ForEach-Object {Set-DnsClientServerAddress -InterfaceIndex $_ -ServerAddresses (\"::1\", \"2001:4860:4860::8888\")}',
        ]));
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
        if (!$this->installed()) {
            return;
        }
        $this->stop();
        $this->cli->run('cmd /C "' . $this->path('AcrylicUI.exe') . '" UninstallAcrylicService', function ($code, $output) {
            CDevSuite::warning("Failed to uninstall Acrylic DNS: $output");
        });
        $this->removeNetworkDNS();

        $this->flushdns();
    }

    /**
     * Determine if the Acrylic DNS is installed.
     *
     * @return bool
     */
    protected function installed(): bool {
        return $this->cli->powershell('Get-Service -Name "AcrylicDNSProxySvc"')->isSuccessful();
    }

    /**
     * Remove the Network DNS.
     *
     * @return void
     */
    protected function removeNetworkDNS() {
        $this->cli->powershell(implode(';', [
            '(Get-NetIPAddress -AddressFamily IPv4).InterfaceIndex | ForEach-Object {Set-DnsClientServerAddress -InterfaceIndex $_ -ResetServerAddresses}',
            '(Get-NetIPAddress -AddressFamily IPv6).InterfaceIndex | ForEach-Object {Set-DnsClientServerAddress -InterfaceIndex $_ -ResetServerAddresses}',
        ]));
    }

    /**
     * Start the Acrylic DNS service.
     *
     * @return void
     */
    public function start() {
        $this->cli->runOrExit('cmd /C "' . $this->path('AcrylicUI.exe') . '" StartAcrylicService', function ($code, $output) {
            CDevSuite::error("Failed to start Acrylic DNS: $output");
        });

        $this->flushdns();
    }

    /**
     * Stop the Acrylic DNS service.
     *
     * @return void
     */
    public function stop() {
        $this->cli->run('cmd /C "' . $this->path('AcrylicUI.exe') . '" StopAcrylicService', function ($code, $output) {
            CDevSuite::warning("Failed to stop Acrylic DNS: $output");
        });

        $this->flushdns();
    }

    /**
     * Restart the Acrylic DNS service.
     *
     * @return void
     */
    public function restart() {
        $this->cli->run('cmd /C "' . $this->path('AcrylicUI.exe') . '" RestartAcrylicService', function ($code, $output) {
            CDevSuite::warning("Failed to restart Acrylic DNS: $output");
        });
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
     * @param string $path
     *
     * @return string
     */
    public function path(string $path = ''): string {
        $basePath = str_replace(DIRECTORY_SEPARATOR, '/', realpath(CDevSuite::binPath() . 'acrylic/'));

        return $basePath . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }
}

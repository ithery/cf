<?php

use Symfony\Component\Process\PhpExecutableFinder;

class CDevSuite_Windows_PhpCgi {
    const PORT = 9001;

    /**
     * @var CDevSuite_Windows_CommandLine
     */
    protected $cli;

    /**
     * @var CDevSuite_Windows_Filesystem
     */
    protected $files;

    /**
     * @var CDevSuite_Windows_WinSW
     */
    protected $winsw;

    /**
     * @var CDevSuite_Windows_Configuration
     */
    protected $configuration;

    /**
     * Create a new PHP CGI class instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $winswFactory = new CDevSuite_Windows_WinSWFactory();
        $this->winsw = $winswFactory->make('phpcgiservice');
        $this->configuration = CDevSuite::configuration();
    }

    /**
     * Install and configure PHP CGI service.
     *
     * @return void
     */
    public function install() {
        CDevSuite::info('Installing PHP-CGI service...');

        $this->installService();
    }

    /**
     * Install the Windows service.
     *
     * @return void
     */
    public function installService() {
        if ($this->winsw->installed()) {
            $this->winsw->uninstall();
        }

        $this->winsw->install([
            'PHP_PATH' => $this->findPhpPath(),
            'PHP_PORT' => $this->configuration->get('php_port', CDevSuite_Windows_PhpCgi::PORT),
        ]);

        $this->winsw->restart();
    }

    /**
     * Uninstall the PHP CGI service.
     *
     * @return void
     */
    public function uninstall() {
        $this->winsw->uninstall();
    }

    /**
     * Restart the PHP CGI service.
     *
     * @return void
     */
    public function restart() {
        $this->winsw->restart();
    }

    /**
     * Stop the PHP CGI service.
     *
     * @return void
     */
    public function stop() {
        $this->winsw->stop();
    }

    /**
     * Find the PHP path.
     *
     * @return string
     */
    protected function findPhpPath(): string {
        if (!$php = (new PhpExecutableFinder())->find()) {
            $php = $this->cli->runOrExit('where php', function () {
                error('Failed to find PHP. Make sure it\'s added to the path environment variables.');
            });
        }

        return pathinfo(explode("\n", $php)[0], PATHINFO_DIRNAME);
    }
}

<?php

/**
 * Description of PhpFpm.
 *
 * @author Hery
 */
use Symfony\Component\Process\Process;

class CDevSuite_Windows_PhpFpm extends CDevSuite_PhpFpm {
    const SERVICE = 'phpfpmservice';

    public $cli;

    public $files;

    public $winsw;

    /**
     * Create a new PHP FPM class instance.
     *
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->winsw = CDevSuite::winsw();
    }

    /**
     * Install and configure PHP FPM.
     *
     * @return void
     */
    public function install() {
        $this->uninstall();
        $this->winsw->install(static::SERVICE, ['PHP_PATH' => $this->findPhpPath()]);

        $this->restart();
    }

    /**
     * Restart the PHP FPM process.
     *
     * @return void
     */
    public function restart() {
        $this->winsw->restart(static::SERVICE);
    }

    /**
     * Stop the PHP FPM process.
     *
     * @return void
     */
    public function stop() {
        $this->winsw->stop(static::SERVICE);
    }

    /**
     * Prepare PHP FPM for uninstallation.
     *
     * @return void
     */
    public function uninstall() {
        $this->winsw->uninstall(static::SERVICE);
    }

    /**
     * Find the PHP path.
     *
     * @return string
     */
    public function findPhpPath() {
        $php = $this->cli->runOrExit('where php', function ($code, $output) {
            CDevSuite::warning('Could not find PHP. Make sure it\'s added to the environment variables.');
        });

        return pathinfo(explode("\n", $php)[0], PATHINFO_DIRNAME);
    }
}

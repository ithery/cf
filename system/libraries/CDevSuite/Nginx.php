<?php

/**
 * Description of Nginx
 *
 * @author Hery
 */
abstract class CDevSuite_Nginx {

    /**
     *
     * @var CDevSuite_CommandLine
     */
    public $cli;

    /**
     *
     * @var CDevSuite_Filesystem
     */
    public $files;

    /**
     *
     * @var CDevSuite_Configuration
     */
    public $configuration;

    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->configuration = CDevSuite::configuration();
        $this->files = CDevSuite::filesystem();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    abstract public function install();

    /**
     * Forcefully uninstall Nginx.
     *
     * @return void
     */
    abstract public function uninstall();

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    abstract public function stop();

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    abstract public function restart();
}

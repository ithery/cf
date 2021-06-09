<?php

/**
 * Description of DnsMasq
 *
 * @author Hery
 */
abstract class CDevSuite_DnsMasq {
    public $cli;

    public $files;

    public $configuration;

    /**
     * Create a new DnsMasq instance.
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->files = CDevSuite::filesystem();
        $this->configuration = CDevSuite::configuration();
    }

    /**
     * Install and configure DnsMasq.
     *
     * @param string $tld
     *
     * @return void
     */
    abstract public function install($tld = 'test');

    /**
     * Forcefully uninstall dnsmasq.
     *
     * @return void
     */
    abstract public function uninstall();
}

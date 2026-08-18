<?php

/**
 * Description of DnsMasq.
 */
abstract class CDevSuite_DnsMasq {
    public $cli;

    /**
     * @var CDevSuite_Filesystem_Linux
     */
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

    /**
     * Update the TLD/domain resolved by DnsMasq.
     *
     * @param string $oldTld Old TLD
     * @param string $newTld New TLD
     *
     * @return void
     */
    abstract public function updateTld($oldTld, $newTld);
}

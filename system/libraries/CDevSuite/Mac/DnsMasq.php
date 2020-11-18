<?php

/**
 * Description of DnsMasq
 *
 * @author Hery
 */
class CDevSuite_Mac_DnsMasq extends CDevSuite_DnsMasq {

    public $brew;
    public $dnsmasqMasterConfigFile = BREW_PREFIX . '/etc/dnsmasq.conf';
    public $dnsmasqSystemConfDir = BREW_PREFIX . '/etc/dnsmasq.d';
    public $resolverPath = '/etc/resolver';

    /**
     * Create a new DnsMasq instance.
     */
    public function __construct() {
        parent::__construct();
        $this->brew = CDevSuite::brew();
    }

    /**
     * Install and configure DnsMasq.
     *
     * @return void
     */
    public function install($tld = 'test') {
        $this->brew->ensureInstalled('dnsmasq');

        // For DnsMasq, we enable its feature of loading *.conf from /usr/local/etc/dnsmasq.d/
        // and then we put a devsuite config file in there to point to the user's home .config/devsuite/dnsmasq.d
        // This allows DevSuite to make changes to our own files without needing to modify the core dnsmasq configs
        $this->ensureUsingDnsmasqDForConfigs();

        $this->createDnsmasqTldConfigFile($tld);

        $this->createTldResolver($tld);

        $this->brew->restartService('dnsmasq');

        CDevSuite::info('DevSuite is configured to serve for TLD [.' . $tld . ']');
    }

    /**
     * Forcefully uninstall dnsmasq.
     * 
     * @return void
     */
    function uninstall() {
        $this->brew->stopService('dnsmasq');
        $this->brew->uninstallFormula('dnsmasq');
        $this->cli->run('rm -rf ' . BREW_PREFIX . '/etc/dnsmasq.d/dnsmasq-devsuite.conf');
        $tld = $this->configuration->read()['tld'];
        $this->files->unlink($this->resolverPath . '/' . $tld);
    }

    /**
     * Tell Homebrew to restart dnsmasq
     * 
     * @return void
     */
    function restart() {
        $this->brew->restartService('dnsmasq');
    }

    /**
     * Ensure the DnsMasq configuration primary config is set to read custom configs
     *
     * @return void
     */
    function ensureUsingDnsmasqDForConfigs() {
        CDevSuite::info('Updating Dnsmasq configuration...');

        // set primary config to look for configs in /usr/local/etc/dnsmasq.d/*.conf
        $contents = $this->files->get($this->dnsmasqMasterConfigFile);
        // ensure the line we need to use is present, and uncomment it if needed
        if (false === strpos($contents, 'conf-dir=' . BREW_PREFIX . '/etc/dnsmasq.d/,*.conf')) {
            $contents .= PHP_EOL . 'conf-dir=' . BREW_PREFIX . '/etc/dnsmasq.d/,*.conf' . PHP_EOL;
        }
        $contents = str_replace('#conf-dir=' . BREW_PREFIX . '/etc/dnsmasq.d/,*.conf', 'conf-dir=' . BREW_PREFIX . '/etc/dnsmasq.d/,*.conf', $contents);

        // remove entries used by older DevSuite versions:
        $contents = preg_replace('/^conf-file.*devsuite.*$/m', '', $contents);

        // save the updated config file
        $this->files->put($this->dnsmasqMasterConfigFile, $contents);

        // remove old ~/.config/devsuite/dnsmasq.conf file because things are moved to the ~/.config/devsuite/dnsmasq.d/ folder now
        if (file_exists($file = dirname($this->dnsmasqUserConfigDir()) . '/dnsmasq.conf')) {
            unlink($file);
        }

        // add a devsuite-specific config file to point to user's home directory devsuite config
        $contents = $this->files->get(CDevSuite::stubsPath() . 'etc-dnsmasq-devsuite.conf');
        $contents = str_replace('DEVSUITE_HOME_PATH', rtrim(CDevSuite::homePath(),'/'), $contents);
        $this->files->ensureDirExists($this->dnsmasqSystemConfDir, CDevSuite::user());
        $this->files->putAsUser($this->dnsmasqSystemConfDir . '/dnsmasq-devsuite.conf', $contents);

        $this->files->ensureDirExists(CDevSuite::homePath() . '/dnsmasq.d', CDevSuite::user());
    }

    /**
     * Create the TLD-specific dnsmasq config file
     * @param  string  $tld
     * @return void
     */
    function createDnsmasqTldConfigFile($tld) {
        $tldConfigFile = $this->dnsmasqUserConfigDir() . 'tld-' . $tld . '.conf';

        $this->files->putAsUser($tldConfigFile, 'address=/.' . $tld . '/127.0.0.1' . PHP_EOL . 'listen-address=127.0.0.1' . PHP_EOL);
    }

    /**
     * Create the resolver file to point the configured TLD to 127.0.0.1.
     *
     * @param  string  $tld
     * @return void
     */
    function createTldResolver($tld) {
        $this->files->ensureDirExists($this->resolverPath);

        $this->files->put($this->resolverPath . '/' . $tld, 'nameserver 127.0.0.1' . PHP_EOL);
    }

    /**
     * Update the TLD/domain resolved by DnsMasq.
     *
     * @param  string  $oldTld
     * @param  string  $newTld
     * @return void
     */
    function updateTld($oldTld, $newTld) {
        $this->files->unlink($this->resolverPath . '/' . $oldTld);
        $this->files->unlink($this->dnsmasqUserConfigDir() . 'tld-' . $oldTld . '.conf');

        $this->install($newTld);
    }

    /**
     * Get the custom configuration path.
     *
     * @return string
     */
    function dnsmasqUserConfigDir() {
        return $_SERVER['HOME'] . '/.config/devsuite/dnsmasq.d/';
    }

}

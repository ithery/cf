<?php

/**
 * Description of DnsMasq
 *
 * @author Hery
 */
class CDevSuite_Linux_DnsMasq extends CDevSuite_DnsMasq {
    public $pm;

    public $sm;

    public $rclocal;

    /**
     * @var string
     */
    public $configPath;

    /**
     * @var string
     */
    public $nmConfigPath;

    /**
     * @var string
     */
    public $resolvedConfigPath;

    /**
     * @var string
     */
    public $dnsMasqConf;
    /**
     * @var string
     */
    public $dnsMasqOpts;
    /**
     * @var string
     */
    public $resolvConf;

    /**
     * Create a new DnsMasq instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->pm = CDevSuite::packageManager();
        $this->sm = CDevSuite::serviceManager();
        $this->rclocal = '/etc/rc.local';
        $this->resolvConf = '/etc/resolv.conf';
        $this->dnsMasqConf = '/etc/dnsmasq.conf';
        $this->configPath = '/etc/dnsmasq.d/devsuite';
        $this->dnsMasqOpts = '/etc/dnsmasq.d/options';
        $this->nmConfigPath = '/etc/NetworkManager/conf.d/devsuite.conf';
        $this->resolvedConfigPath = '/etc/systemd/resolved.conf';
    }

    /**
     * Install and configure DnsMasq.
     *
     * @param bool $lock Lock or Unlock the file
     *
     * @return void
     */
    private function lockResolvConf($lock = true) {
        $arg = $lock ? '+i' : '-i';

        if (!$this->files->isLink($this->resolvConf)) {
            $this->cli->run(
                "chattr {$arg} {$this->resolvConf}",
                function ($code, $msg) {
                    CDevSuite::warning($msg);
                }
            );
        }
    }

    /**
     * Enable nameserver merging
     *
     * @return void
     */
    private function mergeDns() {
        $optDir = '/opt/devsuite-linux';
        $script = $optDir . '/devsuite-dns';

        $this->pm->ensureInstalled('inotify-tools');
        $this->files->removeAsRoot($optDir);
        $this->files->ensureDirExistsAsRoot($optDir);

        $this->files->copyAsRoot(CDevSuite::stubsPath() . 'devsuite-dns', $script);

        //$this->files->putAsRoot($script, $this->files->get(CDevSuite::stubsPath(). 'devsuite-dns'));
        $this->cli->run("sudo chmod +x $script");
        $this->sm->installDevSuiteDns($this->files);

        if ($this->files->exists($this->rclocal)) {
            $this->files->restore($this->rclocal);
        }

        $this->files->backupAsRoot($this->resolvConf);
        $this->files->unlinkAsRoot($this->resolvConf);
        $this->files->symlinkAsRoot($script, $this->resolvConf);

        return true;
    }

    /**
     * Install and configure DnsMasq.
     *
     * @param string $tld Domain TLD to use
     *
     * @return void
     */
    public function install($tld = 'test') {
        $this->dnsmasqSetup();
        $this->fixResolved();
        $this->createCustomConfigFile($tld);
        $this->pm->nmRestart($this->sm);
        $this->sm->restart('dnsmasq');
        $this->sm->start('devsuite-dns');
    }

    /**
     * Append the custom DnsMasq configuration file to the main configuration file.
     *
     * @param string $domain Domain TLD to use
     *
     * @return void
     */
    public function createCustomConfigFile($domain) {
        $this->files->putAsRoot($this->configPath, 'address=/.' . $domain . '/127.0.0.1' . PHP_EOL);
    }

    /**
     * Fix systemd-resolved configuration.
     *
     * @return void
     */
    public function fixResolved() {
        // $resolved = $this->resolvedConfigPath;
        // $this->files->backup($resolved);
        // $this->files->putAsUser($resolved, $this->files->get(__DIR__.'/../stubs/resolved.conf'));

        $this->sm->disable('systemd-resolved');
        $this->sm->stop('systemd-resolved');
    }

    /**
     * Setup dnsmasq with Network Manager.
     *
     * @return void
     */
    public function dnsmasqSetup() {
        $this->pm->ensureInstalled('dnsmasq');
        $this->sm->enable('dnsmasq');

        $this->files->ensureDirExists('/etc/NetworkManager/conf.d');
        $this->files->ensureDirExists('/etc/dnsmasq.d');

        $this->files->uncommentLine('IGNORE_RESOLVCONF', '/etc/default/dnsmasq');

        $this->lockResolvConf(false);
        $this->mergeDns();

        $this->files->unlink('/etc/dnsmasq.d/network-manager');
        $this->files->backupAsRoot($this->dnsMasqConf);

        $this->files->putAsRoot($this->dnsMasqConf, $this->files->get(CDevSuite::stubsPath() . 'dnsmasq.conf'));
        $this->files->putAsRoot($this->dnsMasqOpts, $this->files->get(CDevSuite::stubsPath() . 'dnsmasq_options'));
        $this->files->putAsRoot($this->nmConfigPath, $this->files->get(CDevSuite::stubsPath() . 'networkmanager.conf'));
    }

    /**
     * Update the domain used by DnsMasq.
     *
     * @param string $oldDomain Old TLD
     * @param string $newDomain New TLD
     *
     * @return void
     */
    public function updateDomain($oldDomain, $newDomain) {
        $this->createCustomConfigFile($newDomain);
        $this->sm->restart('dnsmasq');
    }

    /**
     * Delete the DnsMasq config file.
     *
     * @return void
     */
    public function uninstall() {
        $this->sm->stop('devsuite-dns');
        $this->sm->disable('devsuite-dns');

        $this->cli->passthru('sudo rm -rf /opt/devsuite-linux');
        $this->files->unlinkAsRoot($this->configPath);
        $this->files->unlinkAsRoot($this->dnsMasqOpts);
        $this->files->unlinkAsRoot($this->nmConfigPath);
        $this->files->restore($this->resolvedConfigPath);

        $this->lockResolvConf(false);
        $this->files->restoreAsRoot($this->rclocal);
        $this->files->restoreAsRoot($this->resolvConf);
        $this->files->restoreAsRoot($this->dnsMasqConf);
        $this->files->commentLine('IGNORE_RESOLVCONF', '/etc/default/dnsmasq');

        $this->pm->nmRestart($this->sm);
        $this->sm->restart('dnsmasq');

        CDevSuite::info('DevSuite DNS changes have been rolled back');
        CDevSuite::warning('If your system depended on systemd-resolved (like Ubuntu 17.04), please enable it manually');
    }
}

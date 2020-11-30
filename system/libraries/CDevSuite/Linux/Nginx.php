<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CDevSuite_Linux_Nginx extends CDevSuite_Nginx {

    public $pm;
    public $sm;
    public $cli;
    public $files;
    public $configuration;
    public $site;
    public $nginx_conf;
    public $sites_available_conf;
    public $sites_enabled_conf;

    /**
     * Create a new Nginx instance.
     *
     * @param PackageManager $pm
     * @param ServiceManager $sm
     * @param CommandLine    $cli
     * @param Filesystem     $files
     * @param Configuration  $configuration
     * @param Site           $site
     * @return void
     */
    public function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->pm = CDevSuite::packageManager();
        $this->sm = CDevSuite::serviceManager();
        $this->site = CDevSuite::site();
        $this->files = CDevSuite::filesystem();
        $this->configuration = CDevSuite::configuration();
        $this->nginx_conf = '/etc/nginx/nginx.conf';
        $this->sites_available_conf = '/etc/nginx/sites-available/devsuite.conf';
        $this->sites_enabled_conf = '/etc/nginx/sites-enabled/devsuite.conf';
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    public function install() {
        $this->pm->ensureInstalled('nginx');
        $this->sm->enable('nginx');
        $this->files->ensureDirExistsAsRoot('/etc/nginx/sites-available');
        $this->files->ensureDirExistsAsRoot('/etc/nginx/sites-enabled');

        $this->stop();
        $this->installConfiguration();
        $this->installServer();
        $this->installNginxDirectory();
    }

    /**
     * Install the Nginx configuration file.
     *
     * @return void
     */
    public function installConfiguration() {
        $contents = $this->files->get(CDevSuite::stubsPath() . 'nginx.conf');
        $nginx = $this->nginx_conf;

        $pid_string = 'pid /run/nginx.pid';
        $hasPIDoption = strpos($this->cli->run('cat /lib/systemd/system/nginx.service'), 'pid /');

        if ($hasPIDoption) {
            $pid_string = '# pid /run/nginx.pid';
        }

        $this->files->backupAsRoot($nginx);
        CDevSuite::info('Creating file:'.$nginx);
        $this->files->putAsRoot(
                $nginx, str_replace(['DEVSUITE_USER', 'DEVSUITE_GROUP','DEVSUITE_HOME_PATH', 'DEVSUITE_PID']
                        , [CDevSuite::user(), CDevSuite::group(), rtrim(CDevSuite::homePath(), '/'),$pid_string]
                        , $contents)
        );

    }

    /**
     * Install the DevSuite Nginx server configuration file.
     *
     * @return void
     */
    public function installServer() {
        CDevSuite::info('Creating file:'.$this->sites_available_conf);
        $this->files->putAsRoot(
                $this->sites_available_conf,
                str_replace(
                        ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_PORT'],
                        [rtrim(CDevSuite::homePath(),'/'), CDevSuite::serverPath(), CDevSuite::staticPrefix(), $this->configuration->read()['port']],
                        $this->files->get(CDevSuite::stubsPath() . 'devsuite.conf')
                )
        );

        if ($this->files->exists('/etc/nginx/sites-enabled/default')) {
            $this->files->unlinkAsRoot('/etc/nginx/sites-enabled/default');
        }

        $this->cli->run("sudo ln -snf {$this->sites_available_conf} {$this->sites_enabled_conf}");
        $this->files->backupAsRoot('/etc/nginx/fastcgi_params');

        CDevSuite::info('Creating file:'.'/etc/nginx/fastcgi_params');
        $this->files->putAsRoot(
                '/etc/nginx/fastcgi_params',
                $this->files->get(CDevSuite::stubsPath() . 'fastcgi_params')
        );
    }

    /**
     * Install the Nginx configuration directory to the ~/.devsuite directory.
     *
     * This directory contains all site-specific Nginx servers.
     *
     * @return void
     */
    public function installNginxDirectory() {
        if (!$this->files->isDir($nginxDirectory = CDevSuite::homePath() . '/Nginx')) {
            $this->files->mkdirAsUser($nginxDirectory);
        }

        $this->files->putAsUser($nginxDirectory . '/.keep', "\n");

        $this->rewriteSecureNginxFiles();
    }

    /**
     * Update the port used by Nginx.
     *
     * @param string $newPort
     * @return void
     */
    public function updatePort($newPort) {
        $this->files->putAsRoot(
                $this->sites_available_conf,
                str_replace(
                        ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'DEVSUITE_PORT'],
                        [rtrim(CDevSuite::homePath(),'/'), CDevSuite::serverPath(), CDevSuite::staticPrefix(), $newPort],
                        $this->files->get(CDevSuite::stubsPath() . 'devsuite.conf')
                )
        );
    }

    /**
     * Generate fresh Nginx servers for existing secure sites.
     *
     * @return void
     */
    public function rewriteSecureNginxFiles() {
        $configuration = $this->configuration->read();
        $domain = isset($configuration['tld']) ? $configuration['tld'] : null;

        if (!$domain) {
            return;
        }

        $this->site->resecureForNewDomain($domain, $domain);
    }

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    public function restart() {
        $this->sm->restart('nginx');
    }

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    public function stop() {
        $this->sm->stop('nginx');
    }

    /**
     * Nginx service status.
     *
     * @return void
     */
    public function status() {
        $this->sm->printStatus('nginx');
    }

    /**
     * Prepare Nginx for uninstallation.
     *
     * @return void
     */
    public function uninstall() {
        $this->stop();
        $this->files->restoreAsRoot($this->nginx_conf);
        $this->files->restoreAsRoot('/etc/nginx/fastcgi_params');
        $this->files->unlinkAsRoot($this->sites_enabled_conf);
        $this->files->unlinkAsRoot($this->sites_available_conf);

        if ($this->files->exists('/etc/nginx/sites-available/default')) {
            $this->files->symlinkAsRoot('/etc/nginx/sites-available/default', '/etc/nginx/sites-enabled/default');
        }
    }

}

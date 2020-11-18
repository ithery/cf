<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Nov 15, 2020 
 * @license Ittron Global Teknologi
 */
class CDevSuite_Mac_Nginx extends CDevSuite_Nginx {

    public $brew;
    public $cli;
    public $files;
    public $configuration;
    public $site;

    const NGINX_CONF = '/usr/local/etc/nginx/nginx.conf';

    /**
     * Create a new Nginx instance.
     * @return void
     */
    function __construct() {
        $this->cli = CDevSuite::commandLine();
        $this->brew = CDevSuite::brew();
        $this->site = CDevSuite::site();
        $this->files = CDevSuite::filesystem();
        $this->configuration = CDevSuite::configuration();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    function install() {
        if (!$this->brew->hasInstalledNginx()) {
            $this->brew->installOrFail('nginx', []);
        }

        $this->installConfiguration();
        $this->installServer();
        $this->installNginxDirectory();
    }

    /**
     * Install the Nginx configuration file.
     *
     * @return void
     */
    function installConfiguration() {
        CDevSuite::info('Installing nginx configuration...');

        $contents = $this->files->get(CDevSuite::stubsPath().'nginx.conf');

        $this->files->putAsUser(
                static::NGINX_CONF, str_replace(['DEVSUITE_USER', 'DEVSUITE_HOME_PATH'], [CDevSuite::user(), CDevSuite::homePath()], $contents)
        );
    }

    /**
     * Install the DevSuite Nginx server configuration file.
     *
     * @return void
     */
    function installServer() {
        $this->files->ensureDirExists('/usr/local/etc/nginx/devsuite');

        $this->files->putAsUser(
                '/usr/local/etc/nginx/devsuite/devsuite.conf', str_replace(
                        ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX'], 
                        [CDevSuite::homePath(), CDevSuite::serverPath(), CDevSuite::staticPrefix()], $this->files->get(CDevSuite::stubsPath() . 'devsuite.conf')
                )
        );

        $this->files->putAsUser(
                '/usr/local/etc/nginx/fastcgi_params', $this->files->get(CDevSuite::stubsPath().'fastcgi_params')
        );
    }

    /**
     * Install the Nginx configuration directory to the ~/.config/devsuite directory.
     *
     * This directory contains all site-specific Nginx servers.
     *
     * @return void
     */
    function installNginxDirectory() {
        CDevSuite::info('Installing nginx directory...');

        if (!$this->files->isDir($nginxDirectory = CDevSuite::homePath() . '/Nginx')) {
            $this->files->mkdirAsUser($nginxDirectory);
        }

        $this->files->putAsUser($nginxDirectory . '/.keep', "\n");

        $this->rewriteSecureNginxFiles();
    }

    /**
     * Check nginx.conf for errors.
     */
    private function lint() {
        $this->cli->run(
                'sudo nginx -c ' . static::NGINX_CONF . ' -t', function ($exitCode, $outputMessage) {
            throw new DomainException("Nginx cannot start; please check your nginx.conf [$exitCode: $outputMessage].");
        }
        );
    }

    /**
     * Generate fresh Nginx servers for existing secure sites.
     *
     * @return void
     */
    function rewriteSecureNginxFiles() {
        $tld = $this->configuration->read()['tld'];

        $this->site->resecureForNewTld($tld, $tld);
    }

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    function restart() {
        $this->lint();

        $this->brew->restartService($this->brew->nginxServiceName());
    }

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    function stop() {
        CDevSuite::info('Stopping nginx...');

        $this->cli->quietly('sudo brew services stop ' . $this->brew->nginxServiceName());
    }

    /**
     * Forcefully uninstall Nginx.
     *
     * @return void
     */
    function uninstall() {
        $this->brew->stopService(['nginx', 'nginx-full']);
        $this->brew->uninstallFormula('nginx nginx-full');
        $this->cli->quietly('rm -rf /usr/local/etc/nginx /usr/local/var/log/nginx');
    }

}

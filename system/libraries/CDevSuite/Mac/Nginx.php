<?php

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Nov 15, 2020
 */
class CDevSuite_Mac_Nginx extends CDevSuite_Nginx {
    const NGINX_CONF = '/usr/local/etc/nginx/nginx.conf';

    /**
     * @var CDevSuite_Brew
     */
    public $brew;

    /**
     * @var CDevsuite_Mac_Site
     */
    public $site;

    /**
     * Create a new Nginx instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->brew = CDevSuite::brew();
        $this->site = CDevSuite::site();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    public function install() {
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
    public function installConfiguration() {
        CDevSuite::info('Installing nginx configuration...');

        $contents = $this->files->get(CDevSuite::stubsPath() . 'nginx.conf');

        $this->files->putAsRoot(
            static::NGINX_CONF,
            str_replace(['DEVSUITE_USER', 'DEVSUITE_HOME_PATH'], [CDevSuite::user(), rtrim(CDevSuite::homePath(), '/')], $contents)
        );
    }

    /**
     * Install the DevSuite Nginx server configuration file.
     *
     * @return void
     */
    public function installServer() {
        $this->files->ensureDirExistsAsRoot('/usr/local/etc/nginx/devsuite');

        $this->files->putAsRoot(
            '/usr/local/etc/nginx/devsuite/devsuite.conf',
            str_replace(
                ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX'],
                [rtrim(CDevSuite::homePath(), '/'), CDevSuite::serverPath(), CDevSuite::staticPrefix()],
                $this->files->get(CDevSuite::stubsPath() . 'devsuite.conf')
            )
        );

        $this->files->putAsRoot(
            '/usr/local/etc/nginx/fastcgi_params',
            $this->files->get(CDevSuite::stubsPath() . 'fastcgi_params')
        );
    }

    /**
     * Install the Nginx configuration directory to the ~/.config/devsuite directory.
     *
     * This directory contains all site-specific Nginx servers.
     *
     * @return void
     */
    public function installNginxDirectory() {
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
            'sudo nginx -c ' . static::NGINX_CONF . ' -t',
            function ($exitCode, $outputMessage) {
                throw new DomainException("Nginx cannot start; please check your nginx.conf [${exitCode}: ${outputMessage}].");
            }
        );
    }

    /**
     * Generate fresh Nginx servers for existing secure sites.
     *
     * @return void
     */
    public function rewriteSecureNginxFiles() {
        $tld = $this->configuration->read()['tld'];
        $loopback = $this->configuration->read()['loopback'];

        if ($loopback !== CDevSuite::loopback()) {
            $this->site->aliasLoopback(CDevSuite::loopback(), $loopback);
        }

        $config = compact('tld', 'loopback');

        $this->site->resecureForNewConfiguration($config, $config);
    }

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    public function restart() {
        $this->lint();

        $this->brew->restartService($this->brew->nginxServiceName());
    }

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    public function stop() {
        CDevSuite::info('Stopping nginx...');

        $this->cli->quietly('sudo brew services stop ' . $this->brew->nginxServiceName());
    }

    /**
     * Forcefully uninstall Nginx.
     *
     * @return void
     */
    public function uninstall() {
        $this->brew->stopService(['nginx', 'nginx-full']);
        $this->brew->uninstallFormula('nginx nginx-full');
        $this->cli->quietly('rm -rf /usr/local/etc/nginx /usr/local/var/log/nginx');
    }
}

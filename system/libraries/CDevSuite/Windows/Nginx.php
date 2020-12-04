<?php

/**
 * Description of Nginx
 *
 * @author Hery
 */
class CDevSuite_Windows_Nginx extends CDevSuite_Nginx {

    /**
     *
     * @var CDevSuite_Windows_Site
     */
    public $site;

    /**
     *
     * @var CDevSuite_Winsw
     */
    public $winsw;

    const SERVICE = 'nginxservice';

    /**
     * Create a new Nginx instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->site = CDevSuite::site();
        $this->winsw = CDevSuite::winsw();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    public function install() {
        $this->installConfiguration();
        $this->installServer();
        $this->installNginxDirectory();
        $this->installService();
    }

    /**
     * Install the Nginx configuration file.
     *
     * @return void
     */
    public function installConfiguration() {
        CDevSuite::info('Installing nginx configuration...');

        $contents = $this->files->get(CDevSuite::stubsPath() . 'nginx.conf');

        $this->files->putAsUser(
                $this->path() . '/conf/nginx.conf', str_replace(['DEVSUITE_USER', 'DEVSUITE_HOME_PATH'],
                        [CDevSuite::user(), rtrim(CDevSuite::homePath(), '/')], $contents)
        );
    }

    /**
     * Install the DevSuite Nginx server configuration file.
     *
     * @return void
     */
    public function installServer() {
        $this->files->ensureDirExists($this->path() . '/devsuite');

        $this->files->putAsUser(
                $this->path() . '/devsuite/devsuite.conf', str_replace(
                        ['DEVSUITE_HOME_PATH', 'DEVSUITE_SERVER_PATH', 'DEVSUITE_STATIC_PREFIX', 'HOME_PATH'],
                        [rtrim(CDevSuite::homePath(), '/'), rtrim(CDevSuite::serverPath(), '/'), CDevSuite::staticPrefix(), $_SERVER['HOME']], $this->files->get(CDevSuite::stubsPath() . 'devsuite.conf')
                )
        );

        $this->files->putAsUser(
                $this->path() . '/conf/fastcgi_params', $this->files->get(CDevSuite::stubsPath() . 'fastcgi_params')
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
     * Generate fresh Nginx servers for existing secure sites.
     *
     * @return void
     */
    public function rewriteSecureNginxFiles() {
        $tld = $this->configuration->read()['tld'];

        $this->site->resecureForNewTld($tld, $tld);
    }

    /**
     * Install the Windows service.
     *
     * @return void
     */
    public function installService() {
        $this->uninstall();

        $this->winsw->install(static::SERVICE, [
            'NGINX_PATH' => realpath(CDevSuite::binPath() . 'nginx'),
        ]);
    }

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    public function restart() {
        $this->stop();

        $this->winsw->restart(static::SERVICE);
    }

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    public function stop() {
        CDevSuite::info('Stopping nginx...');

        $this->winsw->stop(static::SERVICE);

        $this->cli->run('cmd "/C taskkill /IM nginx.exe /F"');
    }

    /**
     * Prepare Nginx for uninstallation.
     *
     * @return void
     */
    public function uninstall() {
        $this->winsw->uninstall(static::SERVICE);
    }

    /**
     * Get the Nginx path.
     *
     * @return string
     */
    public function path() {
        return realpath(CDevSuite::binPath() . 'nginx');
    }

}

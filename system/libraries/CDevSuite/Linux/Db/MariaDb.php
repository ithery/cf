<?php

/**
 * Description of MariaDb
 *
 * @author Hery
 */
class CDevSuite_Linux_Db_MariaDB extends CDevSuite_Db_MariaDb {
    public $pm;

    public $sm;

    /**
     * @var CDevSuite_Linux_Filesystem
     */
    public $files;

    /**
     * Create a new MariaDb instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->pm = CDevSuite::packageManager();
        $this->sm = CDevSuite::serviceManager();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    public function install() {
        $this->pm->ensureInstalled('mariadb-server');
        $this->sm->enable('mariadb-server');

        $this->stop();
        $this->installMariaDbDirectory();
    }

    /**
     * Stop the MariaDB service.
     *
     * @return void
     */
    public function stop() {
        $this->sm->stop('mariadb-server');
    }

    /**
     * Restart the MariaDB service.
     *
     * @return void
     */
    public function restart() {
        $this->sm->restart('mariadb-server');
    }

    /**
     * Nginx service status.
     *
     * @return void
     */
    public function status() {
        $this->sm->printStatus('mariadb-server');
    }

    /**
     * Prepare MariaDB for uninstallation.
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

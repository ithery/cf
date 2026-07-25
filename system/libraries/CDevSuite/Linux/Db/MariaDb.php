<?php

/**
 * Description of MariaDb
 */
class CDevSuite_Linux_Db_MariaDB extends CDevSuite_Db_MariaDb {
    /**
     * @var CDevSuite_PackageManager
     */
    public $pm;

    /**
     * @var CDevSuite_ServiceManager
     */
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
     * Install the configuration files for MariaDB.
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
     * MariaDB service status.
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
        $this->sm->disable('mariadb-server');
    }
}

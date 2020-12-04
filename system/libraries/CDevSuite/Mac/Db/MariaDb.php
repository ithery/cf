<?php

/**
 * Description of MariaDb
 *
 * @author Hery
 */
class CDevSuite_Mac_Db_MariaDB extends CDevSuite_Db_MariaDb {

    public $brew;

    const NGINX_CONF = '/usr/local/etc/nginx/nginx.conf';

    /**
     * Create a new MariaDb instance.
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->brew = CDevSuite::brew();
    }

    /**
     * Install the configuration files for Nginx.
     *
     * @return void
     */
    public function install() {
        $this->installMariaDbDirectory();
        if (!$this->brew->hasInstalledMariaDb()) {
            $this->brew->installOrFail('mariadb', []);
        }

        $this->cli->run('mysql_install_db');
    }

}

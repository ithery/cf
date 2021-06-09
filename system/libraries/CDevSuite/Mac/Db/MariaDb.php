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
     *
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

    /**
     * Stop the Nginx service.
     *
     * @return void
     */
    public function stop() {
        //CDevSuite::info('Stopping nginx...');

        //$this->cli->quietly('sudo brew services stop ' . $this->brew->nginxServiceName());
    }

    /**
     * Forcefully uninstall Nginx.
     *
     * @return void
     */
    public function uninstall() {
        //$this->brew->stopService(['nginx', 'nginx-full']);
        //$this->brew->uninstallFormula('nginx nginx-full');
        //$this->cli->quietly('rm -rf /usr/local/etc/nginx /usr/local/var/log/nginx');
    }

    /**
     * Restart the Nginx service.
     *
     * @return void
     */
    public function restart() {
        //$this->lint();

        $this->brew->restartService($this->brew->mariaDbServiceName());
    }

    protected function getDumperBinaryPath() {
        return '';
    }

    protected function getClientBinaryPath() {
        return '';
    }
}

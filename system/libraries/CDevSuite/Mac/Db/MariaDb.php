<?php

/**
 * Description of MariaDb
 */
class CDevSuite_Mac_Db_MariaDB extends CDevSuite_Db_MariaDb {
    /**
     * @var CDevSuite_Brew
     */
    public $brew;

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
     * Install MariaDB via Homebrew.
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
     * Stop the MariaDB service.
     *
     * @return void
     */
    public function stop() {
        $this->brew->stopService($this->brew->mariaDbServiceName());
    }

    /**
     * Forcefully uninstall MariaDB.
     *
     * @return void
     */
    public function uninstall() {
        $this->brew->stopService($this->brew->mariaDbServiceName());
        $this->brew->uninstallFormula('mariadb');
    }

    /**
     * Restart the MariaDB service.
     *
     * @return void
     */
    public function restart() {
        //$this->lint();

        $this->brew->restartService($this->brew->mariaDbServiceName());
    }

    /**
     * Get the path to the mysqldump binary.
     *
     * @return string
     */
    protected function getDumperBinaryPath() {
        return '';
    }

    /**
     * Get the path to the mysql client binary.
     *
     * @return string
     */
    protected function getClientBinaryPath() {
        return '';
    }
}

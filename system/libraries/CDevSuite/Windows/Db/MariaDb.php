<?php

/**
 * Description of MariaDB.
 */
class CDevSuite_Windows_Db_MariaDb extends CDevSuite_Db_MariaDb {
    const SERVICE = 'mariadbservice';

    /**
     * @var CDevSuite_Winsw
     */
    public $winsw;

    /**
     * Create a new MariaDb instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
        $this->winsw = CDevSuite::winsw();
    }

    /**
     * Install the configuration files for MariaDb.
     *
     * @return void
     */
    public function install() {
        $this->installMariaDbDirectory();
        $this->installService();
    }

    /**
     * Stop the MariaDb service.
     *
     * @return void
     */
    public function stop() {
        CDevSuite::info('Stopping mariadb...');

        $this->winsw->stop(static::SERVICE);

        //$this->cli->run('cmd "/C taskkill /IM mysqld.exe /F"');
    }

    /**
     * Stop and start the MariaDb service.
     *
     * @return void
     */
    public function start() {
        $this->stop();
        CDevSuite::info('Starting mariadb...');
        $this->winsw->restart(static::SERVICE);
    }

    /**
     * Restart the MariaDb service.
     *
     * @return void
     */
    public function restart() {
        $this->start();
    }

    /**
     * Install the Windows service.
     *
     * @return void
     */
    public function installService() {
        $this->uninstall();
        $mysqldPath = $this->path() . DS . 'bin' . DS . 'mysqld.exe';
        $mysqlIniPath = $this->mariaDbIniFile();
        $this->winsw->install(static::SERVICE, [
            'MARIADB_PATH' => realpath(CDevSuite::binPath() . 'mariadb'),
            'MARIADB_INI_FILE' => $mysqlIniPath,
        ]);
    }

    /**
     * Prepare MariaDb for uninstallation.
     *
     * @return void
     */
    public function uninstall() {
        $this->winsw->uninstall(static::SERVICE);
    }

    /**
     * Get the MariaDB path.
     *
     * @return string
     */
    public function path() {
        $path = realpath(CDevSuite::binPath() . 'mariadb');
        if (!is_dir($path)) {
            return '';
        }

        return $path;
    }

    /**
     * Get socket path.
     *
     * @return string
     */
    public function getSocketPath() {
        return realpath(CDevSuite::binPath() . 'mariadb') . '/devsuite/mariadb/mariadb.sock';
    }
}

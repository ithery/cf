<?php

/**
 * Description of MariaDB
 *
 * @author Hery
 */
class CDevSuite_Windows_Db_MariaDb extends CDevSuite_Db_MariaDb {
    /**
     * @var CDevSuite_Winsw
     */
    public $winsw;

    const SERVICE = 'mariadbservice';

    public function __construct() {
        parent::__construct();
        $this->winsw = CDevSuite::winsw();
    }

    public function install() {
        $this->installMariaDbDirectory();
        $this->installService();
    }

    public function stop() {
        CDevSuite::info('Stopping mariadb...');

        $this->winsw->stop(static::SERVICE);

        //$this->cli->run('cmd "/C taskkill /IM mysqld.exe /F"');
    }

    public function start() {
        $this->stop();
        CDevSuite::info('Starting mariadb...');
        $this->winsw->restart(static::SERVICE);
    }

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

    public function getSocketPath() {
        return realpath(CDevSuite::binPath() . 'mariadb') . '/devsuite/mariadb/mariadb.sock';
    }
}

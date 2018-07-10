<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:20:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Storage {

    protected static $instance;
    protected $freeSpace;
    protected $totalSpace;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new CServer_Storage();
        }
        return self::$instance;
    }

    /**
     * 
     * @return float
     */
    public function getFreeSpace() {
        if ($this->freeSpace == null) {
            $this->freeSpace = disk_free_space(".");
        }
        return $this->freeSpace;
    }

    public function getTotalSpace() {
        if ($this->totalSpace == null) {
            $this->totalSpace = disk_total_space("/");
        }
        return $this->totalSpace;
    }

}

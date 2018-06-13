<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 13, 2018, 10:20:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CServer_Storage {

    public static function getFreeSpace() {
        return disk_free_space(".");
    }

    public static function getTotalSpace() {
        return disk_total_space("/");
    }

}

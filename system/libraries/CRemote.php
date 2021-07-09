<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 7, 2018, 1:46:57 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CRemote {

    public static function ssh($config) {
        return new CRemote_SSH($config);
    }

}

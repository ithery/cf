<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 7, 2018, 1:46:57 AM
 */
class CRemote {
    public static function ssh($config) {
        return new CRemote_SSH($config);
    }
}

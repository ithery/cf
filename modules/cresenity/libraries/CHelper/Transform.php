<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 30, 2019, 7:11:34 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CHelper_Transform {

    public static function formatSize($bytes) {
        return CHelper_Formatter::formatSize($bytes);
    }

}

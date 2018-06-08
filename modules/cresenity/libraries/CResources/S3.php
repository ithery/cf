<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 29, 2018, 10:57:46 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CResources_S3 {

    public static function createCloud($cloudName, $options = array()) {
        $className = 'CResources_S3_Cloud_' . $cloudName;
        return new $className($options);
    }

}

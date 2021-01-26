<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 29, 2018, 10:57:46 PM
 */
class CResources_S3 {
    public static function createCloud($cloudName, $options = []) {
        $className = 'CResources_S3_Cloud_' . $cloudName;
        return new $className($options);
    }
}

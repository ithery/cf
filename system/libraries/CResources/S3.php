<?php

defined('SYSPATH') or die('No direct access allowed.');

class CResources_S3 {
    public static function createCloud($cloudName, $options = []) {
        $className = 'CResources_S3_Cloud_' . $cloudName;

        return new $className($options);
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:40:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_Resources {

    /**
     * 
     * @deprecated since version 1.2, please use function getFileInfo
     * @param type $filename
     * @return array
     */
    public static function get_file_info($filename) {
        return self::getFileInfo($filename);
    }

    /**
     * 
     * @deprecated since version 1.2, please use function getPath
     * @param type $filename
     * @param type $size
     * @return type
     */
    public static function get_path($filename, $size = null) {
        return self::getPath($filename, $size);
    }

    /**
     * @deprecated
     * @param string $name
     * @param array $options
     * @return \CResources_Loader_File
     */
    public static function files($name, $options = array()) {
        return self::file($name, $options);
    }

}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 5:40:40 AM
 */
//@codingStandardsIgnoreStart
trait CTrait_Compat_Resources {
    /**
     * @param type $filename
     *
     * @return array
     *
     * @deprecated since version 1.1, please use function getFileInfo
     */
    public static function get_file_info($filename) {
        return self::getFileInfo($filename);
    }

    /**
     * @deprecated since version 1.2, please use function getPath
     *
     * @param type $filename
     * @param type $size
     *
     * @return type
     */
    public static function get_path($filename, $size = null) {
        return self::getPath($filename, $size);
    }

    /**
     * @deprecated
     *
     * @param string $name
     * @param array  $options
     *
     * @return \CResources_Loader_File
     *
     * @deprecated 1.1
     */
    public static function files($name, $options = []) {
        return self::file($name, $options);
    }

    /**
     * @param type  $org_code
     * @param type  $app_code
     * @param mixed $resource_type
     * @param mixed $depth
     *
     * @deprecated version
     */
    public static function get_all_file($org_code, $app_code, $resource_type, $depth = 0) {
        $root_directory = DOCROOT . 'application' . DS . $app_code . DS . 'default' . DS . 'resources' . DS . $org_code . DS . $resource_type;
        $files = CResources::scanDirectory($root_directory);
        return $files;
    }
}
//@codingStandardsIgnoreEnd

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 */
final class CDomain {
    public static function path() {
        $dir = DOCROOT . 'data' . DS . 'domain' . DS;
        return $dir;
    }

    public static function get($domain) {
        $file = self::path() . $domain . EXT;

        $data = null;
        if (CFile::exists($file)) {
            $data = CFile::getRequire($file);
        } else {
            //search maybe found in wildcard file
            $dataNameExploded = explode('.', $domain);
            if (count($dataNameExploded) > 0) {
                $fileWildcard = '$.' . implode('.', array_slice($dataNameExploded, 1));

                if (file_exists($fileWildcard . EXT)) {
                    return CFile::getRequire($fileWildcard . EXT);
                }
            }
        }
        return $data;
    }

    public static function set($domain, $data) {
        $file = self::path() . $domain . EXT;

        CFile::putPhpValue($file, $data);

        return true;
    }

    public static function delete($domain) {
        $file = self::path() . $domain . EXT;
        if (CFile::exists($file)) {
            CFile::delete($domain);
        }
    }
}

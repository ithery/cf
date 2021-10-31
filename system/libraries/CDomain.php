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

    public static function getFile($dataName) {
        $file = self::path();
        $file .= $dataName;

        return $file;
    }

    public static function get($domain) {
        $file = self::path() . $domain . EXT;

        $data = null;
        if (CFile::exists($file)) {
            return CFile::getRequire($file);
        }
        //search maybe found in wildcard file
        $dataNameExploded = explode('.', $domain);
        if (count($dataNameExploded) > 0) {
            $fileWildcard = '$.' . implode('.', array_slice($dataNameExploded, 1));
            $file = static::getFile($fileWildcard);
            if (CFile::exists($file . EXT)) {
                return CFile::getRequire($file . EXT);
            }
        }
        $file = static::getFile('$');
        if (file_exists($file . EXT)) {
            return CFile::getRequire($file . EXT);
        }

        return null;
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

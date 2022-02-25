<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2018, 1:54:30 AM
 */
class CManager_Asset_Helper {
    public static function urlCssFile($file, $withHttp = false) {
        //return CResource::instance('css')->url($file);
        $docroot = str_replace(DS, '/', DOCROOT);
        $file = str_replace(DS, '/', $file);
        $path = carr::first(explode('?', $file));

        $base_url = curl::base();
        if ($withHttp) {
            $base_url = curl::base(false, 'http');
        }
        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.css.versioning')) {
            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $interval = CF::config('assets.css.interval', 0);
            $version = static::getFileVersion($path, $interval);
            $file .= $separator . 'v=' . $version;
        }

        return $file;
    }

    public static function urlJsFile($file, $withHttp = false) {
        if ($file instanceof CManager_Asset_File_JsFile) {
            return $file->getUrl();
        }
        $path = $file;
        $path = carr::first(explode('?', $file));
        $docroot = str_replace(DS, '/', DOCROOT);
        $file = str_replace(DS, '/', $file);
        $base_url = curl::base();
        if ($withHttp) {
            $base_url = curl::base(false, 'http');
        }

        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.js.versioning')) {
            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $interval = CF::config('assets.js.interval', 0);
            $version = static::getFileVersion($path, $interval);
            $file .= $separator . 'v=' . $version;
        }

        return $file;
    }

    public static function getFileVersion($file, $interval = 0) {
        $version = filemtime($file);
        if ($interval) {
            $roundVar = $interval * 60;
            $mod = $version % $roundVar;
            $version = $version - $mod;
        }

        return $version;
    }

    public static function fullpathCssFile($file, $mediaPaths = []) {
        foreach ($mediaPaths as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }
        $dirs = CF::getDirs('media');
        $dirs = array_merge($mediaPaths, $dirs);

        foreach ($dirs as $dir) {
            $path = $dir . 'css' . DS . $file;

            if (file_exists($path)) {
                return $path;
            }
        }
        $path = DOCROOT . 'media' . DS . 'css' . DS;

        return $path . $file;
    }
}

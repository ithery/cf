<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 1:54:30 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CManager_Asset_Helper {

    public static function urlCssFile($file) {
        //return CResource::instance('css')->url($file);
        $docroot = str_replace(DS, "/", DOCROOT);
        $file = str_replace(DS, "/", $file);
        $path = carr::first(explode('?', $file));

        $base_url = curl::base();
        if (CManager::instance()->isMobile()) {
            $base_url = curl::base(false, 'http');
        }
        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.css.versioning')) {

            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $file .= $separator . 'v=' . filemtime($path);
        }

        return $file;
    }

    public static function urlJsFile($file) {
        $path = $file;
        $path = carr::first(explode('?', $file));
        $docroot = str_replace(DS, "/", DOCROOT);
        $file = str_replace(DS, "/", $file);
        $base_url = curl::base();
        if (CManager::instance()->isMobile()) {

            $base_url = curl::base(false, 'http');
        }

        $file = str_replace($docroot, $base_url, $file);

        if (CF::config('assets.js.versioning')) {
            $separator = parse_url($file, PHP_URL_QUERY) ? '&' : '?';
            $file .= $separator . 'v=' . filemtime($path);
        }

        return $file;
    }

}

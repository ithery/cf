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
        $base_url = curl::base();
        if (CManager::instance()->isMobile()) {
            $base_url = curl::base(false, 'http');
        }
        $file = str_replace($docroot, $base_url, $file);

        return $file;
    }

    public static function urlJsFile($file) {
        
        $docroot = str_replace(DS, "/", DOCROOT);
        $file = str_replace(DS, "/", $file);
        $base_url = curl::base();
        if (CManager::instance()->isMobile()) {

            $base_url = curl::base(false, 'http');
        }
        
        $file = str_replace($docroot, $base_url, $file);

        return $file;
    }

}

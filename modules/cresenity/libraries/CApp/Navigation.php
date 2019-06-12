<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 2:40:04 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

use CApp_Navigation_Helper as Helper;

class CApp_Navigation {

    public static $instance = array();
    protected static $accessCallback = array();
    protected static $activeCallback = array();

    /**
     * 
     * @param callable $navigationCallback
     * @param string $domain optional
     */
    public static function getAccessCallback($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        return carr::get(self::$accessCallback, $domain);
    }
    
    /**
     * 
     * @param callable $navigationCallback
     * @param string $domain optional
     */
    public static function getActiveCallback($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        return carr::get(self::$activeCallback, $domain);
    }

    /**
     * 
     * @param callable $navigationCallback
     * @param string $domain optional
     */
    public static function setAccessCallback(callable $accessCallback, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        self::$accessCallback[$domain] = $accessCallback;
    }
    
    /**
     * 
     * @param callable $navigationCallback
     * @param string $domain optional
     */
    public static function setActiveCallback(callable $activeCallback, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        self::$activeCallback[$domain] = $activeCallback;
    }

    /**
     * 
     * @param string $appCode
     * @return CApp_Navigation
     */
    public static function instance($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }

        if (!isset(self::$instance[$domain])) {
            self::$instance[$domain] = new CApp_Navigation($domain);
        }
        return self::$instance[$domain];
    }

    public function __construct($domain = null) {
        
    }

    public static function navs($domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $navsArray = CApp_Navigation_Data::get($domain);

        return $navsArray;
    }

    /**
     * 
     * @param array $options 
     * @return html of the element
     */
    public static function render($options = array()) {

        $engine = carr::get($options, 'engine', 'Bootstrap');
        $layout = carr::get($options, 'layout', 'horizontal');

        $engineClassName = 'CApp_Navigation_Engine_' . $engine;
        $engineClass = new $engineClassName();
        return $engineClass->render();
    }

    public function filterNavWithAccess($navs = null, $level = 0, &$child = 0, $domain = null) {
        if ($domain == null) {
            $domain = CF::domain();
        }
        $is_admin = CApp::instance()->isAdmin();
        if ($navs == null && $level == 0) {
            $navs = static::navs($domain);
        }
        if ($navs == null) {
            return false;
        }
        $filteredNavs = array();
        
        $html = "";
        $child_count = 0;
            
        foreach ($navs as $d) {
            $clonedNav = $d;
            $child = 0;
            $pass = 0;
            $activeClass = "";
            $controller = carr::get($clonedNav, 'controller');
            $method = carr::get($clonedNav, 'method');
            $label = carr::get($clonedNav, 'label');
            $icon = carr::get($clonedNav, 'icon');
            
            if (strlen($controller)>0) {
                if (!$is_admin && ccfg::get("have_user_access")) {

                    if (!Helper::haveAccess($d)) {
                        continue;
                    }
                }
            }
            
            $haveSubnav = isset($d["subnav"]) && is_array($d["subnav"]);
            $subnavArray = array();
            if ($haveSubnav) {
                $subnavArray = static::filterNavWithAccess(carr::get($d, 'subnav', array()), $level + 1, $child);
                $clonedNav['subnav']=$subnavArray;
            }

            $filteredNavs[] = $clonedNav;
          

           

            $url = Helper::url($d);

            if (!isset($url) || $url == null)
                $url = "";

            if (count($subnavArray)>0) {

                if (!Helper::accessAvailable($d, CF::appId(), $domain)) {

                    continue;
                }
                if (isset($d["controller"]) && $d["controller"] != "") {
                    if (!$is_admin && ccfg::get("have_user_access")) {

                        if (!Helper::haveAccess($d)) {
                            continue;
                        }
                    }
                }

                $child_count++;

                
            }
        }
        

        return $filteredNavs;
    }

}

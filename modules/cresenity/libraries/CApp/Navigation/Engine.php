<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 1, 2018, 12:11:34 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
use \CApp_Navigation_Helper as Helper;

class CApp_Navigation_Engine implements CApp_Navigation_EngineInterface {

    protected $roleNavs = array();
    protected $roleId;
    protected $appId;
    
    public function __construct($options=array()) {
        $app = CApp::instance();
        
        $roleId = carr::get($options,'role_id');
        $appId = carr::get($options,'app_id');
        
        if ($roleId == null) {
            $role = $app->role();
            if ($role != null)
                $roleId = $role->role_id;
        }
        if ($app_id == null) {
            $app_id = $app->app_id();
        }
        if ($nav == null)
            $nav = cnav::nav();

        if ($nav === false)
            return false;
        $db = CDatabase::instance($domain);
        if ($role_id == "PUBLIC") {
            $role_id = null;
        }

        $role = crole::get($role_id);
        if ($role != null) {
            if ($role->parent_id == null)
                return true;
        }
        if (!isset(self::$role_navs[$app_id]) || !isset(self::$role_navs[$app_id][$role_id])) {
            if (!isset(self::$role_navs[$app_id])) {
                self::$role_navs[$app_id] = array();
            }
            if (!isset(self::$role_navs[$app_id][$role_id])) {

                $q = "select nav from role_nav where role_id=" . $db->escape($role_id) . " and app_id=" . $db->escape($app_id);
                if ($role_id == null) {
                    $q = "select nav from role_nav where role_id is null and app_id=" . $db->escape($app_id);
                }
                self::$role_navs[$app_id][$role_id] = cdbutils::get_array($q);
            }
        }
    }
    
}

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
    protected $roleId = null;
    protected $appId = null;
    protected $navs = null;

    public function __construct($options = array()) {
        $app = CApp::instance();
        $db = CDatabase::instance();

        $roleId = carr::get($options, 'role_id');
        $appId = carr::get($options, 'app_id');
        $navs = carr::get($options, 'navs');

        if ($roleId == null) {
            $role = $app->role();
            if ($role != null)
                $roleId = $role->role_id;
        }
        if ($appId == null) {
            $appId = CF::appId();
        }
        $this->roleId = $roleId;
        $this->appId = $appId;

        /* get nav */
        if ($navs == null) {
            $navs = CApp_Navigation_Data::get();
        }

        $this->navs = $navs;

        $q = "select nav from role_nav where role_id=" . $db->escape($roleId) . " and app_id=" . $db->escape($appId);
        if ($roleId == null) {
            $q = "select nav from role_nav where role_id is null and app_id=" . $db->escape($appId);
        }
        $this->roleNavs = cdbutils::get_array($q);
    }

    public static function have_access($nav = null, $roleId = null, $appId = null, $domain = null) {

        $app = CApp::instance();
        if ($roleId == null) {
            $role = $app->role();
            if ($role != null)
                $roleId = $role->role_id;
        }
        if ($appId == null) {
            $appId = CF::appId();
        }
        if ($nav == null)
            $nav = cnav::nav();

        if ($nav === false)
            return false;
        $db = CDatabase::instance($domain);
        if ($roleId == "PUBLIC") {
            $roleId = null;
        }

        $role = crole::get($roleId);
        if ($role != null) {
            if ($role->parent_id == null)
                return true;
        }


        if (!isset(self::$role_navs[$appId]) || !isset(self::$role_navs[$appId][$roleId])) {
            if (!isset(self::$role_navs[$appId])) {
                self::$role_navs[$appId] = array();
            }
            if (!isset(self::$role_navs[$appId][$roleId])) {
                $roleNavModel = CApp::model('RoleNav')->where('role_id', 'is', null);
                //$q = "select nav from role_nav where role_id is null and app_id=" . $db->escape($appId);
                if ($roleId != null) {
                    $roleNavModel = CApp::model('RoleNav')->where('role_id', '=', $roleId);
                    //$q = "select nav from role_nav where role_id=" . $db->escape($roleId) . " and app_id=" . $db->escape($appId);
                }
                self::$role_navs[$appId][$roleId] = $roleNavModel->where('app_id', '=', $appId)->get()->pluck('word_two')->toArray();
                //self::$role_navs[$appId][$roleId] = cdbutils::get_array($q);
            }
        }

        return in_array($nav["name"], self::$role_navs[$appId][$roleId]);
    }

}

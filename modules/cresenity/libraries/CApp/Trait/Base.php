<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Dec 30, 2017, 3:11:24 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_Base {

    private static $org = null;

    /**
     * 
     * @return int
     */
    public static function org_id() {
        $org_id = CF::org_id();
        $app = CApp::instance();
        if ($app->user() != null) {
            if (strlen($app->user()->org_id) > 0) {
                $org_id = $app->user()->org_id;
            }
        }
        return $org_id;
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Code of org
     */
    public static function org_code($org_id = null) {
        $org = self::org($org_id);
        return cobj::get($org, 'code');
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Name of org
     */
    public static function org_name($org_id = null) {
        $org = self::org($org_id);
        return cobj::get($org, 'name');
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return stdClass of org
     */
    public static function org($org_id = null) {
        $db = CDatabase::instance();

        if ($org_id == null) {
            $org_id = self::org_id();
        }
        if (self::$org == null) {
            self::$org = array();
        }
        if (!isset(self::$org[$org_id])) {
            self::$org[$org_id] = cdbutils::get_row('select * from org where org_id = ' . $db->escape($org_id));
        }
        return self::$org[$org_id];
    }

    /**
     * return current CSession object
     * 
     * @return CSession
     */
    public static function session() {
        return CSession::instance();
    }

    /**
     * 
     * @return string value of current theme
     */
    public static function theme() {
        $theme = CF::theme();

        return $theme;
    }

    /**
     * User dari session CApp
     * 
     * @return stdClass 
     */
    public static function user() {
        $session = self::session();
        $user = $session->get('user');
        return $user;
    }

    /**
     * User ID dari session CApp
     * 
     * @return int
     */
    public static function user_id() {
        return cobj::get(self::user(), 'user_id');
    }

    /**
     * Current Date Y-m-d H:i:s format
     * 
     * @return string
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }

}

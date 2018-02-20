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
    public static function orgId() {
        $org_id = CF::org_id();
        $app = CApp::instance();
        if ($app->user() != null) {
            if (strlen($app->user()->org_id) > 0) {
                $org_id = $app->user()->org_id;
            }
        }
        return $org_id;
    }

    public static function org_id() {
        return self::orgId();
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Code of org
     */
    public static function orgCode($orgId = null) {
        $org = self::org($orgId);
        return cobj::get($org, 'code');
    }

    public static function org_code($orgId = null) {
        return self::orgCode($orgId);
    }

    /**
     * 
     * @param int $org_id optional, default using return values of SM::org_id()
     * @return string Name of org
     */
    public static function orgName($orgId = null) {
        $org = self::org($orgId);
        return cobj::get($org, 'name');
    }

    public static function org_name($orgId = null) {
        return self::orgName($orgId);
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
    public static function userId() {
        return cobj::get(self::user(), 'user_id');
    }

    public static function user_id() {
        return self::userId();
    }

    /**
     * Current Date Y-m-d H:i:s format
     * 
     * @return string
     */
    public static function now() {
        return date('Y-m-d H:i:s');
    }

    /**
     * 
     * @return array
     */
    public static function defaultInsert() {
        $data = array();
        $data['created'] = self::now();
        $data['createdby'] = self::username();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        $data['status'] = 1;

        return $data;
    }

    /**
     * 
     * @return array
     */
    public static function defaultUpdate() {
        $data = array();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        return $data;
    }

    /**
     * 
     * @return array
     */
    public static function defaultDelete() {
        $data = array();
        $data['updated'] = self::now();
        $data['updatedby'] = self::username();
        return $data;
    }

}

<?php

use \CApp_Navigation_Helper as Helper;

class cnav {

    public static function nav($nav = null, $controller = null, $method = null, $path = null) {
        return Helper::nav($nav, $controller, $method, $path);
    }

    public static function have_access($nav = null, $role_id = null, $app_id = null, $domain = null) {
        return Helper::haveAccess($nav, $role_id, $app_id, $domain);
    }

    public static function have_permission($action, $nav = null, $role_id = null, $app_id = null, $domain = null) {
        return Helper::havePermission($action, $nav, $role_id, $app_id, $domain);
    }

    public static function app_user_rights_array($app_id, $role_id, $app_role_id = "", $domain = "") {
        return Helper::appUserRightsArray($app_id, $role_id, $app_role_id, $domain);
    }

    public static function as_user_rights_array($app_id, $role_id, $navs = null, $app_role_id = "", $domain = "", $level = 0) {
        return Helper::asUserRightsArray($app_id, $role_id, $navs, $app_role_id, $domain, $level);
    }

    public static function is_public($nav) {
        return Helper::isPublic($nav);
    }

    public static function child_count($nav) {
        return Helper::childCount($nav);
    }

    public static function have_child($nav) {
        return Helper::haveChild($nav);
    }

    public static function is_leaf($nav) {
        return Helper::isLeaf($nav);
    }

    public static function url($nav) {
        return Helper::url($nav);
    }

    public static function access_available($nav = null, $app_id = "", $domain = "", $appRoleId = "") {
        return Helper::accessAvailable($nav, $appId, $domain, $appRoleId);
    }

    public static function permission_available($action, $nav = null, $appId = "", $domain = "", $appRoleId = "") {
        return Helper::permissionAvailable($action, $nav, $appId, $domain, $appRoleId);
    }

    public static function render($navs = null, $level = 0, &$child = 0) {
        return Helper::render($navs, $level, $child);
    }

}

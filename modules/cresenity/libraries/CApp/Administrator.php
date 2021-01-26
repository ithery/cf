<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CApp_Administrator {
    const ADMIN_SESSSION_KEY = 'administrator';

    protected static $navs = [];

    public static function isEnabled() {
        return isset($_COOKIE['capp-administrator']);
    }

    public static function isLogin() {
        $session = CSession::instance();
        $admin = $session->get(self::ADMIN_SESSSION_KEY);
        return $admin != null;
    }

    public static function login($password) {
        if (md5($password) == 'a5d93c9e4eacf2120c6c478064832e8f') {
            $adminData = [
                'name' => 'Administrator',
                'username' => 'administrator',
                'md5_password' => md5($password),
                'login_time' => date('Y-m-d H:i:s'),
            ];

            $session = CSession::instance();
            $admin = $session->set(self::ADMIN_SESSSION_KEY, $adminData);
            return true;
        }
        return false;
    }

    public static function logout() {
        $session = CSession::instance();
        $session->delete(self::ADMIN_SESSSION_KEY);
    }

    public static function addNav($nav) {
        static::$navs[] = $nav;
    }

    public static function getNav() {
        return static::$navs;
    }
}

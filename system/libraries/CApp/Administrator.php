<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 */
class CApp_Administrator {
    const ADMIN_SESSSION_KEY = 'administrator';

    /**
     * @var array
     */
    protected static $navs = [];

    /**
     * @return bool
     */
    public static function isEnabled() {
        return isset($_COOKIE['capp-administrator']);
    }

    /**
     * @return bool
     */
    public static function isLogin() {
        $session = c::session();
        $admin = $session->get(self::ADMIN_SESSSION_KEY);

        return $admin != null;
    }

    /**
     * @param string $password
     *
     * @return bool
     */
    public static function login($password) {
        if (md5($password) == 'a5d93c9e4eacf2120c6c478064832e8f') {
            $adminData = [
                'name' => 'Administrator',
                'username' => 'administrator',
                'md5_password' => md5($password),
                'login_time' => date('Y-m-d H:i:s'),
            ];

            $session = c::session();
            $admin = $session->set(self::ADMIN_SESSSION_KEY, $adminData);

            return true;
        }

        return false;
    }

    /**
     * @return void
     */
    public static function logout() {
        $session = c::session();
        $session->delete(self::ADMIN_SESSSION_KEY);
    }

    /**
     * @param array $nav
     *
     * @return void
     */
    public static function addNav($nav) {
        static::$navs[] = $nav;
    }

    /**
     * @return array
     */
    public static function getNav() {
        return static::$navs;
    }
}

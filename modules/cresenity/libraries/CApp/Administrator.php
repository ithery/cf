<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 8, 2018, 12:18:58 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CApp_Administrator {

    const ADMIN_SESSSION_KEY = 'administrator';

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
            $adminData = array(
                'name' => 'Administrator',
                'username' => 'administrator',
                'md5_password' => md5($password),
                'login_time' => date('Y-m-d H:i:s'),
            );

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

    
   
}

<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated 1.2, use c::session()
 */
class csess {
    public static function get($key) {
        $session = c::session();

        return $session->get($key);
    }

    public static function set($key, $val) {
        $session = CSession::instance();

        return $session->set($key, $val);
    }

    public static function refresh_user_session() {
        $user = static::get('user');
        if ($user != null) {
            $user = cuser::get($user->user_id);
            static::set('user', $user);
        }
    }

    public static function session_id() {
        $session = CSession::instance();

        return $session->id();
    }
}
//@codingStandardsIgnoreEnd

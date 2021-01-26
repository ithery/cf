<?php

//@codingStandardsIgnoreStart
class csess {
    public static function get($key) {
        $session = CSession::instance();
        return $session->get($key);
    }

    public static function set($key, $val) {
        $session = CSession::instance();
        return $session->set($key, $val);
    }

    public static function refresh_user_session() {
        $user = csess::get('user');
        if ($user != null) {
            $user = cuser::get($user->user_id);
            csess::set('user', $user);
        }
    }

    public static function session_id() {
        $session = CSession::instance();
        return $session->id();
    }
}
//@codingStandardsIgnoreEnd

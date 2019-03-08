<?php

class cuser {

    protected static $users = array();

    /**
     * 
     * @param int $userId
     * @return CApp_Model_Users
     * @deprecated
     */
    public static function get($userId) {
        if (!isset(self::$users[$userId])) {
            self::$users[$userId] = CApp_Model::createModel('Users')->find($userId);
        }
        return self::$users[$userId];
    }

    public static function hitCount($userId) {
        $db = CDatabase::instance();
        return cdbutils::get_value("select count(*) from log_request where user_id=" . $db->escape($userId));
    }

}

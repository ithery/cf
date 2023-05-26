<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2 change to c::app()->user()
 */
class cuser {
    public static function get($id) {
        return c::app()->getUser($id);
    }

    public static function hit_count($user_id) {
        $db = CDatabase::instance();

        return cdbutils::get_value('select count(*) from log_request where user_id=' . $db->escape($user_id));
    }
}
//@codingStandardsIgnoreEnd

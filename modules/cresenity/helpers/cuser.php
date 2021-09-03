<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated
 */
class cuser {
    public static function get($id) {
        $db = CDatabase::instance();
        $query = 'select * '
                . 'from users '
                . 'where status > 0 and user_id = ' . $db->escape($id) . '';
        $result = $db->query($query);
        $value = null;
        if ($result->count() > 0) {
            $value = $result[0];
        }
        return $value;
    }

    public static function hit_count($user_id) {
        $db = CDatabase::instance();
        return cdbutils::get_value('select count(*) from log_request where user_id=' . $db->escape($user_id));
    }
}
//@codingStandardsIgnoreEnd

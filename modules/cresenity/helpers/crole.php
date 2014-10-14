<?php

class crole {

    public static function get($id) {
        $db = CDatabase::instance();
        $query = "select * from roles where status>0 and role_id=" . $db->escape($id);
        $result = $db->query($query);
        $value = null;
        if ($result->count() > 0)
            $value = $result[0];
        return $value;
    }

}
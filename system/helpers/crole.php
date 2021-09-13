<?php

//@codingStandardsIgnoreStart
class crole {
    //@codingStandardsIgnoreEnd
    /**
     * Roles cache
     *
     * @var array
     */
    protected static $roles = [];

    public static function get($id) {
        if ($id === null) {
            return null;
        }
        $db = CDatabase::instance();

        if (!isset(self::$roles[$id])) {
            $query = 'select * from roles where status>0 and role_id=' . $db->escape($id);
            $result = $db->query($query);
            self::$roles[$id] = null;
            if ($result->count() > 0) {
                self::$roles[$id] = $result[0];
            }
        }

        return self::$roles[$id];
    }
}

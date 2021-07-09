<?php

class CApp_Auth_Role {
    /**
     * Roles cache
     *
     * @var array
     */
    protected static $roles = [];

    public static function getModel($id, $refresh = false) {
        if ($id === null) {
            return null;
        }

        if ($refresh && isset(static::$roles[$id])) {
            unset(static::$roles[$id]);
        }
        if (!isset(static::$roles[$id])) {
            static::$roles[$id] = null;
            $role = CApp::model('Roles')->find($id);

            if ($role != null) {
                static::$roles[$id] = $role;
            }
        }

        return static::$roles[$id];
    }
}

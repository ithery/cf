<?php

class CApp_Auth_Role {
    /**
     * Roles cache.
     *
     * @var array
     */
    protected static $roles = [];

    /**
     * Get the role model instance for the given id, using an in-memory cache.
     *
     * @param null|int|string $id      the role id (or a special value such as 'PUBLIC')
     * @param bool            $refresh whether to bypass and refresh the cache
     *
     * @return null|CModel
     */
    public static function getModel($id, $refresh = false) {
        if ($id === null) {
            return null;
        }

        if ($refresh && isset(static::$roles[$id])) {
            unset(static::$roles[$id]);
        }
        if (!isset(static::$roles[$id])) {
            static::$roles[$id] = null;
            $roleClass = c::app()->auth()->getRoleModelClass();
            $role = $roleClass::find($id);
            //$role = CApp::model('Roles')->find($id);

            if ($role != null) {
                static::$roles[$id] = $role;
            }
        }

        return static::$roles[$id];
    }
}

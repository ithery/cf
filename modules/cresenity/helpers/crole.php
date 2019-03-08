<?php

class crole {

    protected static $roles = array();

    /**
     * 
     * @param int $roleId
     * @return CApp_Model_Roles
     * @deprecated
     */
    public static function get($roleId) {
        if (!isset(self::$roles[$roleId])) {
            self::$roles[$roleId] = CApp_Model::createModel('Roles')->find($roleId);
        }
        return self::$roles[$roleId];
    }

}

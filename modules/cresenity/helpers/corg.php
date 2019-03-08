<?php

class corg {

    protected static $org = array();

    /**
     * 
     * @param int $orgId
     * @return CApp_Model_Org
     * @deprecated
     */
    public static function get($orgId) {
        if (!isset(self::$org[$orgId])) {
            self::$org[$orgId] = CApp_Model::createModel('Org')->find($orgId);
        }
        return self::$org[$orgId];
    }

}

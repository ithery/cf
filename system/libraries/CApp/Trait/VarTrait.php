<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Trait_VarTrait {
    /**
     * @var array|null
     */
    protected static $globalVars;

    /**
     * @var array|null
     */
    protected static $vars;

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public static function getGlobalVar($key, $default = null) {
        if (!isset(self::$globalVars[$key])) {
            $db = c::db();
            $value = $db->getValue('select `value` from var where org_id is null and `key`= ' . $db->escape($key));
            if ($value == null) {
                $value = $default;
            }
            self::$globalVars[$key] = $value;
        }

        return self::$globalVars[$key];
    }

    /**
     * @param string $key
     * @param mixed  $val
     *
     * @return bool
     */
    public static function setGlobalVar($key, $val) {
        $db = c::db();
        $row = $db->getRow('select * from var where org_id is null and `key` = ' . $db->escape($key));
        $data['value'] = $val;
        if ($row == null) {
            $data['key'] = $key;
            $data['caption'] = $key;
            $data['org_id'] = null;
            $data['created'] = CApp_Base::now();
            $data['createdby'] = CApp_Base::username();
            $db->insert('var', $data);
        } else {
            $data['updated'] = CApp_Base::now();
            $data['updatedby'] = CApp_Base::username();
            $db->update('var', $data, ['var_id' => $row->var_id]);
        }
        self::$globalVars[$key] = $val;

        return true;
    }

    /**
     * @param string   $key
     * @param int|null $orgId
     * @param mixed    $default
     *
     * @return mixed
     */
    public static function getVar($key, $orgId = null, $default = null) {
        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }
        if (!isset(self::$vars[$orgId])) {
            self::$vars[$orgId] = [];
        }
        if (!isset(self::$vars[$orgId][$key])) {
            $db = c::db();

            $value = $db->getValue('select `value` from var where org_id = ' . $db->escape($orgId) . ' and `key`= ' . $db->escape($key));

            if ($value == null) {
                $value = $default;
            }

            self::$vars[$orgId][$key] = $value;
        }

        return self::$vars[$orgId][$key];
    }

    /**
     * @param string   $key
     * @param mixed    $val
     * @param int|null $orgId
     *
     * @return bool
     */
    public static function setVar($key, $val, $orgId = null) {
        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }
        $db = c::db();

        $row = $db->getRow('select * from var where org_id = ' . $db->escape($orgId) . ' and `key` = ' . $db->escape($key));

        $data['value'] = $val;
        if (!isset(self::$vars[$orgId])) {
            self::$vars[$orgId] = [];
        }

        if ($row == null) {
            $data['key'] = $key;
            $data['caption'] = $key;
            $data['org_id'] = $orgId;
            $data['created'] = CApp_Base::now();
            $data['createdby'] = CApp_Base::username();
            $db->insert('var', $data);
        } else {
            $data['updated'] = CApp_Base::now();
            $data['updatedby'] = CApp_Base::username();
            $db->update('var', $data, ['var_id' => $row->var_id]);
        }
        self::$vars[$orgId][$key] = $val;

        return true;
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2019, 12:02:21 PM
 */

trait CApp_Trait_VarTrait {
    protected static $globalVars;
    protected static $vars;

    public static function getGlobalVar($key, $default = null) {
        if (!isset(self::$globalVars[$key])) {
            $db = CDatabase::instance();
            $value = cdbutils::get_value('select `value` from var where org_id is null and `key`= ' . $db->escape($key));
            if ($value == null) {
                $value = $default;
            }
            self::$globalVars[$key] = $value;
        }
        return self::$globalVars[$key];
    }

    public static function setGlobalVar($key, $val) {
        $db = CDatabase::instance();
        $row = cdbutils::get_row('select * from var where org_id is null and `key` = ' . $db->escape($key));
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

    public static function getVar($key, $orgId = null, $default = null) {
        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }
        if (!isset(self::$vars[$orgId])) {
            self::$vars[$orgId] = [];
        }
        if (!isset(self::$vars[$orgId][$key])) {
            $db = CDatabase::instance();
            $value = cdbutils::get_value('select `value` from var where org_id = ' . $db->escape($orgId) . ' and `key`= ' . $db->escape($key));
            if ($value == null) {
                $value = $default;
            }

            self::$vars[$orgId][$key] = $value;
        }
        return self::$vars[$orgId][$key];
    }

    public static function setVar($key, $val, $orgId = null) {
        if ($orgId == null) {
            $orgId = CApp_Base::orgId();
        }
        $db = CDatabase::instance();
        $row = cdbutils::get_row('select * from var where org_id = ' . $db->escape($orgId) . ' and `key` = ' . $db->escape($key));
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

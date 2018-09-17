<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 10, 2018, 3:05:22 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_Var {

    protected static $vars;

    public static function getGlobalVar($key) {

        if (!isset(self::$vars[$key])) {
            $db = CDatabase::instance();
            $value = $db->getValue("select `value` from `var` where org_id is null and `key`= " . $db->escape($key));
            self::$vars[$key] = $value;
        }
        return self::$vars[$key];
    }

    public static function setGlobalVar($key, $val) {

        $db = CDatabase::instance();
        $row = $db->getRow("select * from `var` where org_id is null and `key` = " . $db->escape($key));
        $data['value'] = $val;
        $data['org_id'] = null;
        if ($row == null) {
            $data['key'] = $key;
            $data['caption'] = $key;
            $data['created'] = date('Y-m-d H:i:s');
            $data['createdby'] = CApp_Base::username();
            $db->insert('var', $data);
        } else {
            $data['updated'] = date('Y-m-d H:i:s');
            $data['updatedby'] = CApp_Base::username();
            $db->update('var', $data, array('var_id' => $row->var_id));
        }
        self::$vars[$key] = $val;
        return true;
    }

}

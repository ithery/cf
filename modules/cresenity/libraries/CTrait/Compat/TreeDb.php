<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 5:40:40 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Compat_TreeDb {

    /**
     * 
     * @deprecated since version 1.2, please use function getFileInfo
     * @param type $filename
     * @return array
     */
    public static function set_display_callback() {
        return self::setDisplayCallback();
    }

    public static function set_pk_column($pk_column) {
        return self::setPkColumn($pk_column);
    }

    public static function set_org_id($id) {
        return self::setOrgId($id);
    }

    public static function add_filter($k, $v) {
        return self::addFilter($k, $v);
    }

    public static function set_delete_child($bool) {
        return self::setDeleteChild($bool);
    }

    public static function set_have_priority($boolean) {
        return self::setHavePriority($boolean);
    }

    public static function filter_where() {
        return self::filterWhere();
    }

    public static function get_list($indent = "") {
        return self::getList($indent = "");
    }

    public static function get_parents($parent_id) {
        return self::getParents($parent_id);
    }

    public static function get_first_parent($parent_id) {
        return self::getFirstParent($parent_id);
    }

    public static function get_children_list($id = null, $indent = "") {
        return self::getChildrenList($id = null, $indent = "");
    }

    public static function get_children_leaf_list($id = null) {
        return self::getChildrenLeafList($id = null);
    }

    public static function get_children_data($id = null) {
        return self::getChildrenData($id = null);
    }

    public static function rebuild_tree_all($force = false) {
        return self::rebuildTreeAll($force = false);
    }

    public static function rebuild_tree_all_ignore_status() {
        return self::rebuildTreeAllIgnoreStatus();
    }

    public static function rebuild_tree($id = null, $left = 1, $depth = 0) {
        return self::rebuildTree($id = null, $left = 1, $depth = 0);
    }

    public static function rebuild_tree_ignore_status($id = null, $left = 1, $depth = 0) {
        return self::rebuildTreeIgnoreStatus($id = null, $left = 1, $depth = 0);
    }

}

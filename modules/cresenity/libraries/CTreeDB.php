<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CTreeDB {

    private $db = null;
    private $pk_column = '';
    private $table_name = '';
    private $org_id = null;
    protected $filters = array();

    public function __construct($table_name, $domain = null, $db = null) {
        if ($db == null)
            $db = CDatabase::instance($domain);
        if ($domain == null)
            $domain = crouter::domain();
        $data = cdata::get($domain, "domain");
        $this->org_id = CF::org_id();


        $this->pk_column = $table_name . "_id";
        if ($table_name == "roles")
            $this->pk_column = 'role_id';
        $this->table_name = $table_name;
        $this->db = $db;
        $this->filters = array();
    }

    public static function factory($table_name, $domain = null, $db = null) {
        return new CTreeDB($table_name, $domain, $db);
    }

    public function set_display_callback() {

        return $this;
    }

    public function add_filter($k, $v) {
        $this->filters[$k] = $v;
        return $this;
    }

    private function filter_where() {
        $where = "";
        $db = $this->db;
        foreach ($this->filters as $k => $v) {
            $where.=" AND " . $db->escape_column($k) . " = " . $db->escape($v);
        }
        //if(strlen($where)>0) $where = substr($where,5);
        return $where;
    }

    public function get_list($indent = "") {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0 and org_id=" . $db->escape($this->org_id) . "
			" . $this->filter_where() . "
			ORDER BY node.lft
		";

        return cdbutils::get_list($q);
    }

    public function insert($data, $parent_id = null) {
        $db = $this->db;
        if ($parent_id != null) {

            $q="select rgt from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $rgt = cdbutils::get_value($q);

            $q="select lft from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $lft = cdbutils::get_value($q);

            $q="select depth from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $depth = cdbutils::get_value($q);

            $q="update " . $db->escape_table($this->table_name) . " set lft=lft+2 where status>0 and  lft>" . $db->escape($rgt - 1);
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $db->query($q);

            $q="update " . $db->escape_table($this->table_name) . " set rgt=rgt+2 where status>0 and  rgt>" . $db->escape($rgt - 1);
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $db->query($q);

            $data['parent_id'] = $parent_id;
            $data['lft'] = $rgt;
            $data['rgt'] = $rgt + 1;

            $data['depth'] = $depth + 1;
        } else {
            $rgt = cdbutils::get_value("select max(rgt) from " . $db->escape_table($this->table_name) . " where org_id=" . $db->escape($this->org_id) . "");

            if ($rgt == null) {
                $rgt = 0;
            }
            $data['lft'] = $rgt + 1;
            $data['rgt'] = $rgt + 2;
            $data['depth'] = 0;
        }

        $r = $db->insert($this->table_name, $data);
        return $r->insert_id();
    }

    public function delete($id) {
        $app = CApp::instance();
        $user = $app->user();

        $db = $this->db;

        $qrgt="select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
        if(strlen($this->org_id)>0){
            $qrgt.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $qlft="select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
        if(strlen($this->org_id)>0){
            $qlft.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $rgt = cdbutils::get_value($qrgt);
        $lft = cdbutils::get_value($qlft);
        $width = $rgt - $lft;
        $q="update " . $db->escape_table($this->table_name) . " set status=0, updated=" . $db->escape(date('Y-m-d H:i:s')) . ",updatedby=" . $db->escape($user->username) . " where lft between " . $db->escape($lft) . " and " . $db->escape($rgt) . " ";
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $db->query($q);
        $q="update " . $db->escape_table($this->table_name) . " set rgt=rgt+" . $db->escape($width) . " where status>0 and rgt>" . $db->escape($rgt);
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $db->query($q);
        $q="update " . $db->escape_table($this->table_name) . " set lft=lft+" . $db->escape($width) . " where status>0 and  lft>" . $db->escape($rgt);
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $db->query($q);
    }

    public function update($id, $data, $parent_id) {
        $app = CApp::instance();
        $user = $app->user();
        $db = $this->db;
        $r = $db->update($this->table_name, $data, array($this->pk_column => $id));

        $this->rebuild_tree_all();
    }

    public function get_parents($id) {
        $db = $this->db;

        $qrgt="select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id)."";
        if(strlen($this->org_id)>0){
            $qrgt.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $qlft="select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id)."";
        if(strlen($this->org_id)>0){
            $qlft.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $rgt = cdbutils::get_value($qrgt);
        $lft = cdbutils::get_value($qlft);

        $q = " select * from " . $db->escape_table($this->table_name) . " where  status>0 and lft<" . $db->escape($lft) . " and rgt>" . $db->escape($rgt) . " ";
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $q.=" order by depth asc";


        $r = $db->query($q);
        return $r;
    }

    public function get_children_list($id = null, $indent = "") {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0

		";

        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $q.=" ORDER BY node.lft";


        if ($id != null) {
            $qrgt="select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qrgt.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $qlft="select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qlft.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $rgt = cdbutils::get_value($qrgt);
            $lft = cdbutils::get_value($qlft);

            $q = "
				SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
				FROM " . $db->escape_table($this->table_name) . " AS node
				WHERE 
					status>0
					and lft>" . $db->escape($lft) . " and rgt<" . $db->escape($rgt) . "

			";
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $q.=" ORDER BY node.lft";
        }
        return cdbutils::get_list($q);
    }

    public function get_children_leaf_list($id = null) {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " ,node.name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0  and rgt=lft+1

		";


        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $q.=" ORDER BY node.lft";



        if ($id != null) {
            $qrgt="select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qrgt.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $qlft="select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qlft.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $rgt = cdbutils::get_value($qrgt);
            $lft = cdbutils::get_value($qlft);


            $q = "
				SELECT " . $db->escape_column($this->pk_column) . " , node.name
				FROM " . $db->escape_table($this->table_name) . " AS node
				WHERE 
					status>0
					and lft>" . $db->escape($lft) . " and rgt<" . $db->escape($rgt) . " and rgt=lft+1
			";

            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $q.=" ORDER BY node.lft";
        }
        $list = cdbutils::get_list($q);

        return $list;
    }

    public function get_children_data($id = null) {
        $db = $this->db;
        $q = "select * from " . $db->escape_table($this->table_name) . " where status>0 " . $this->filter_where() . " order by lft asc";
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        if ($id != null) {
            $qrgt="select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qrgt.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $qlft="select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id)."";
            if(strlen($this->org_id)>0){
                $qlft.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $rgt = cdbutils::get_value($qrgt);
            $lft = cdbutils::get_value($qlft);
            $q = "select * from " . $db->escape_table($this->table_name) . " where status>0 and lft>" . $db->escape($lft) . " and rgt<" . $db->escape($rgt) . " " . $this->filter_where() . " ";
            if(strlen($this->org_id)>0){
                $q.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $q.=" order by lft asc";
        }
        $r = $db->query($q)->result(false);

        return $r;
    }

    function rebuild_tree_all() {
        $db = $this->db;
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where status>0";
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }
        $q.= " and parent_id is null";
        $r = $db->query($q)->result(false);
        $left = 1;

        foreach ($r as $row) {
            $pk = $this->pk_column;
            $this->rebuild_tree($row[$pk], $left);
            $qleft="select rgt from " . $db->escape_table($this->table_name) . " where org_id=" . $db->escape($this->org_id) . " and status>0 and " . $db->escape_column($this->pk_column) . "=" . $db->escape($row[$pk]);
            if(strlen($this->org_id)>0){
                $qleft.=" and org_id=" . $db->escape($this->org_id) . "";
            }
            $left = cdbutils::get_value($qleft) + 1;
        }
    }

    function rebuild_tree($id = null, $left = 1, $depth = 0) {
        // the right value of this node is the left value + 1   
        $db = $this->db;
        $right = $left + 1;
        // get all children of this node   
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where  status>0";
        if(strlen($this->org_id)>0){
            $q.=" and org_id=" . $db->escape($this->org_id) . "";
        }

        if ($id != null) {
            $q.= " and parent_id = " . $db->escape($id);
        } else {
            $q.= " and parent_id is null";
        }
        $r = $db->query($q)->result(false);
        foreach ($r as $row) {
            // recursive execution of this function for each   
            // child of this node   
            // $right is the current right value, which is   
            // incremented by the rebuild_tree function   
            $pk = $this->pk_column;
            $right = $this->rebuild_tree($row[$pk], $right, $depth + 1);
        }

        // we've got the left value, and now that we've processed   
        // the children of this node we also know the right value   

        $data = array(
            'lft' => $left,
            'rgt' => $right,
            'depth' => $depth,
        );
        $db->update($this->table_name, $data, array($this->pk_column => $id));
        // return the right value of this node + 1   
        return $right + 1;
    }

}
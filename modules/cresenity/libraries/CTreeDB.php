<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CTreeDB {
    use CTrait_Compat_TreeDb;

    protected $db = null;
    protected $pk_column = '';
    protected $table_name = '';
    protected $org_id = null;
    protected $filters = array();
    protected $have_priority = false;
    protected $delete_child;

    public function __construct($table_name, $domain = null, $db = null, $prefix = '') {
        if ($domain == null)
            $domain = crouter::domain();
        if ($db == null)
            $db = CDatabase::instance($domain);
        $data = cdata::get($domain, "domain");
        //$this->org_id = CF::org_id();
        $this->org_id = null;

        $pk_column = $table_name . "_id";
        if (strlen($prefix) > 0) {
            $table_name_split = explode($prefix, $table_name);
            if (is_array($table_name_split)) {
                if (isset($table_name_split[1])) {
                    $pk_column = $table_name_split[1] . "_id";
                }
            }
        }

        $this->pk_column = $pk_column;
        if ($table_name == "roles")
            $this->pk_column = 'role_id';
        if ($table_name == "users")
            $this->pk_column = 'user_id';
        $this->table_name = $table_name;
        $this->db = $db;
        $this->filters = array();
        $this->delete_child = false;
    }

    public static function factory($table_name, $domain = null, $db = null, $prefix = '') {
        return new CTreeDB($table_name, $domain, $db, $prefix);
    }

    public function setDisplayCallback() {

        return $this;
    }

    public function setPkColumn($pk_column) {
        $this->pk_column = $pk_column;
        return $this;
    }

    public function setOrgId($id) {
        $this->org_id = $id;

        return $this;
    }

    public function addFilter($k, $v) {
        $this->filters[$k] = $v;
        return $this;
    }

    public function setDeleteChild($bool) {
        $this->delete_child = $bool;
        return $this;
    }

    public function setHavePriority($boolean) {
        $this->have_priority = $boolean;
        return $this;
    }

    protected function filterWhere() {
        $where = "";
        $db = $this->db;
        foreach ($this->filters as $k => $v) {
            if ($v === null) {
                $where .= " AND " . $db->escape_column($k) . " is null";
            } else {
                $where .= " AND " . $db->escape_column($k) . " = " . $db->escape($v);
            }
        }
        //if(strlen($where)>0) $where = substr($where,5);
        return $where;
    }

    public function getList($indent = "") {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0 ";

        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $q .= "
			" . $this->filter_where() . "
			ORDER BY node.lft
		";
        return cdbutils::get_list($q);
    }

    public function insert($data, $parent_id = null) {
        $db = $this->db;
        if ($parent_id != null) {

            $q = "select rgt from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if (strlen($this->org_id) > 0) {
                $q .= " and (org_id=" . $db->escape($this->org_id) . " or org_id is null)";
            }
            $rgt = cdbutils::get_value($q);

            $q = "select lft from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
            }
            $lft = cdbutils::get_value($q);

            $q = "select depth from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
            }
            $depth = cdbutils::get_value($q);

            $q = "update " . $db->escape_table($this->table_name) . " set lft=lft+2 where status>0 and  lft>" . $db->escape($rgt - 1);
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
            }
            $db->query($q);

            $q = "update " . $db->escape_table($this->table_name) . " set rgt=rgt+2 where status>0 and  rgt>" . $db->escape($rgt - 1);
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
            }
            $db->query($q);

            $data['parent_id'] = $parent_id;
            $data['lft'] = $rgt;
            $data['rgt'] = $rgt + 1;

            $data['depth'] = $depth + 1;
        } else {
            $q = "
                select 
                    max(rgt) 
                from 
                    " . $db->escape_table($this->table_name) . "   
                where
                    1=1
            ";
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
            }

            $rgt = cdbutils::get_value($q);

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

        $qrgt = "select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
        if (strlen($this->org_id) > 0) {
            $qrgt .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $qlft = "select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
        if (strlen($this->org_id) > 0) {
            $qlft .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $rgt = cdbutils::get_value($qrgt);
        $lft = cdbutils::get_value($qlft);
        $width = ($rgt - $lft) + 1;
        $q = "update " . $db->escape_table($this->table_name) . " set status=0, updated=" . $db->escape(date('Y-m-d H:i:s')) . ",updatedby=" . $db->escape($user->username) . " where lft between " . $db->escape($lft) . " and " . $db->escape($rgt) . " ";
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $q .= " " . $this->filter_where() . " ";
        $db->query($q);

        if ($this->delete_child == true) {
            $child_list = $this->get_children_list($id);
            if (is_array($child_list) && count($child_list) > 0) {
                foreach ($child_list as $child_list_k => $child_list_v) {
                    $q = "update " . $db->escape_table($this->table_name) . " set status=0, updated=" . $db->escape(date('Y-m-d H:i:s')) . ",updatedby=" . $db->escape($user->username) . " where " . $this->pk_column . "=" . $db->escape($child_list_k);
                    $db->query($q);
                }
            }
        }

        $q = "update " . $db->escape_table($this->table_name) . " set rgt=rgt-" . $db->escape($width) . " where status>0 and rgt>" . $db->escape($rgt);
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }

        $db->query($q);
        $q = "update " . $db->escape_table($this->table_name) . " set lft=lft-" . $db->escape($width) . " where status>0 and  lft>" . $db->escape($rgt);
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }

        $db->query($q);
    }

    public function update($id, $data, $parent_id) {
        $app = CApp::instance();
        $user = $app->user();
        $db = $this->db;

        $r = $db->update($this->table_name, $data, array($this->pk_column => $id));
        if ($this->org_id != null) {
            $this->rebuildTreeAll();
        }
    }

    public function getParents($parent_id) {
        $db = $this->db;

        $q = "SELECT rgt,lft 
                FROM " . $db->escape_table($this->table_name) . " 
                WHERE " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
        $row = cdbutils::get_row($q);
        $rgt = cobj::get($row, 'rgt');
        $lft = cobj::get($row, 'lft');

        $q = " select * from " . $db->escape_table($this->table_name) . " where  status>0 and lft<" . $db->escape($lft) . " and rgt>" . $db->escape($rgt) . " ";
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . " ";
        }
        $q .= $this->filter_where();
        $q .= " order by depth asc";


        $r = $db->query($q);
        return $r;
    }

    public function getFirstParent($parent_id) {
        $db = $this->db;
        $q = "SELECT rgt,lft 
                FROM " . $db->escape_table($this->table_name) . " 
                WHERE " . $db->escape_column($this->pk_column) . " = " . $db->escape($parent_id);
        $row = cdbutils::get_row($q);
        $rgt = cobj::get($row, 'rgt');
        $lft = cobj::get($row, 'lft');

        $q = " select * from " . $db->escape_table($this->table_name) . " where  status>0 and lft<" . $db->escape($lft) . " and rgt>" . $db->escape($rgt) . " ";
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . " or org_id is null";
        }
        $q .= $this->filter_where();
        $q .= " order by lft asc limit 1";

        $r = cdbutils::get_row($q);
        return $r;
    }

    public function getChildrenList($id = null, $indent = "") {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0

		" . $this->filter_where();

        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $q .= " ORDER BY node.lft";


        if ($id != null) {
            $qrgt = "select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
            if (strlen($this->org_id) > 0) {
                $qrgt .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $qlft = "select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
            if (strlen($this->org_id) > 0) {
                $qlft .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $rgt = cdbutils::get_value($qrgt);
            $lft = cdbutils::get_value($qlft);

            $q = "
				SELECT " . $db->escape_column($this->pk_column) . " , CONCAT( REPEAT(" . $db->escape($indent) . ", depth), node.name) AS name
				FROM " . $db->escape_table($this->table_name) . " AS node
				WHERE 
					status>0
					and lft>" . $db->escape($lft) . " and rgt<" . $db->escape($rgt) . "

			" . $this->filter_where();
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $q .= " ORDER BY node.lft";
        }
        return cdbutils::get_list($q);
    }

    public function getChildrenLeafList($id = null) {
        $db = $this->db;

        $q = "
			SELECT " . $db->escape_column($this->pk_column) . " ,node.name
			FROM " . $db->escape_table($this->table_name) . " AS node
			WHERE status>0  and rgt=lft+1

		";


        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $q .= $this->filter_where();
        $q .= " ORDER BY node.lft";



        if ($id != null) {
            $qrgt = "select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
            if (strlen($this->org_id) > 0) {
                $qrgt .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $qlft = "select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";
            if (strlen($this->org_id) > 0) {
                $qlft .= " and org_id=" . $db->escape($this->org_id) . "";
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

            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $q .= " ORDER BY node.lft";
        }
        $list = cdbutils::get_list($q);

        return $list;
    }

    public function getChildrenData($id = null) {
        $db = $this->db;
        $q = "select * from " . $db->escape_table($this->table_name) . " where status>0 " . $this->filter_where();
        if (strlen($this->org_id) > 0 && $this->org_id != 'ALL' && $this->org_id != 'NONE') {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        } else if (strlen($this->org_id) > 0 && $this->org_id == 'NONE') {
            $q .= " and org_id is null ";
        }

        $q .= " order by lft asc";
        if ($id != null) {
            $qrgt = "select rgt from " . $db->escape_table($this->table_name) . " where " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";

            $qlft = "select lft from " . $db->escape_table($this->table_name) . " where  " . $db->escape_column($this->pk_column) . " = " . $db->escape($id) . "";

            $rgt = cdbutils::get_value($qrgt);
            $lft = cdbutils::get_value($qlft);
            $q = "select * from " . $db->escape_table($this->table_name) . " where status>0 and lft>" . $db->escape($lft) . " and rgt<" . $db->escape($rgt) . " " . $this->filter_where() . " ";
            if (strlen($this->org_id) > 0) {
                $q .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $q .= " order by lft asc";
        }

        $r = $db->query($q)->result(false);

        return $r;
    }

    function rebuildTreeAll($force = false) {
        if (!$force) {
            if ($this->org_id == null) {
                throw new Exception('Service Unavailable on rebuild_tree_all on org_id null');
            }
        }
        $db = $this->db;
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where status>0";
        if (strlen($this->org_id) > 0) {
            if ($this->org_id == 'NONE') {
                $q .= " and org_id is null";
            } else {
                $q .= " and org_id=" . $db->escape($this->org_id) . "";
            }
        }

        $q .= " and parent_id is null order by lft," . $db->escape_column($this->pk_column) . " asc";
        $r = $db->query($q)->result(false);
        $left = 1;

        foreach ($r as $row) {
            $pk = $this->pk_column;
            $this->rebuild_tree($row[$pk], $left);
            //$qleft="select rgt from " . $db->escape_table($this->table_name) . " where org_id=" . $db->escape($this->org_id) . " and status>0 and " . $db->escape_column($this->pk_column) . "=" . $db->escape($row[$pk]);
            $qleft = "select rgt from " . $db->escape_table($this->table_name) . " where status>0 and " . $db->escape_column($this->pk_column) . "=" . $db->escape($row[$pk]);
            if (strlen($this->org_id) > 0) {
                if ($this->org_id == 'NONE') {
                    $qleft .= " and org_id is null";
                } else {
                    $qleft .= " and org_id=" . $db->escape($this->org_id) . "";
                }
            }
            $left = cdbutils::get_value($qleft) + 1;
        }
    }

    function rebuildTreeAllIgnoreStatus() {
        $db = $this->db;
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where 1=1 ";
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }
        $q .= " and parent_id is null order by lft asc";
        $r = $db->query($q)->result(false);
        $left = 1;

        foreach ($r as $row) {
            $pk = $this->pk_column;
            $this->rebuild_tree_ignore_status($row[$pk], $left);
            //$qleft="select rgt from " . $db->escape_table($this->table_name) . " where org_id=" . $db->escape($this->org_id) . " and status>0 and " . $db->escape_column($this->pk_column) . "=" . $db->escape($row[$pk]);
            $qleft = "select rgt from " . $db->escape_table($this->table_name) . " where 1=1 and " . $db->escape_column($this->pk_column) . "=" . $db->escape($row[$pk]);
            if (strlen($this->org_id) > 0) {
                $qleft .= " and org_id=" . $db->escape($this->org_id) . "";
            }
            $left = cdbutils::get_value($qleft) + 1;
        }
    }

    function rebuildTree($id = null, $left = 1, $depth = 0) {
        // the right value of this node is the left value + 1   
        $db = $this->db;
        $right = $left + 1;
        // get all children of this node   
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where  status>0";
        if (strlen($this->org_id) > 0) {
            if ($this->org_id == 'NONE') {
                $q .= " and org_id is null";
            } else {
                $q .= " and org_id=" . $db->escape($this->org_id) . "";
            }
        }

        if ($id != null) {
            $q .= " and parent_id = " . $db->escape($id);
        } else {
            $q .= " and parent_id is null";
        }

        if ($this->have_priority) {
            $q .= " ORDER BY priority";
        } else {
            $q .= " ORDER BY lft," . $db->escape_column($this->pk_column) . " asc";
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

    function rebuildTreeIgnoreStatus($id = null, $left = 1, $depth = 0) {
        // the right value of this node is the left value + 1   
        $db = $this->db;
        $right = $left + 1;
        // get all children of this node   
        $q = "select " . $db->escape_column($this->pk_column) . " from " . $db->escape_table($this->table_name) . " where 1=1 ";
        if (strlen($this->org_id) > 0) {
            $q .= " and org_id=" . $db->escape($this->org_id) . "";
        }

        if ($id != null) {
            $q .= " and parent_id = " . $db->escape($id);
        } else {
            $q .= " and parent_id is null";
        }

        if ($this->have_priority) {
            $q .= " ORDER BY priority";
        } else {
            $q .= " ORDER BY lft asc";
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

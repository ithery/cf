<?php

defined('SYSPATH') or die('No direct access allowed.');

class CTreeDB {
    use CTrait_Compat_TreeDb;

    /**
     * @var CDatabase_Connection
     */
    protected $db = null;

    /**
     * @var string
     */
    protected $pkColumn = '';

    /**
     * @var string
     */
    protected $tableName = '';

    /**
     * @var null|int
     */
    protected $orgId = null;

    protected $filters = [];

    protected $have_priority = false;

    protected $delete_child;

    public function __construct($tableName, $domain = null, $db = null, $prefix = '') {
        if ($domain == null) {
            $domain = CF::domain();
        }
        if ($db == null) {
            $db = c::db();
        }
        $this->orgId = null;

        $pkColumn = $tableName . '_id';
        if (strlen($prefix) > 0) {
            $tableName_split = explode($prefix, $tableName);
            if (is_array($tableName_split)) {
                if (isset($tableName_split[1])) {
                    $pkColumn = $tableName_split[1] . '_id';
                }
            }
        }

        $this->pkColumn = $pkColumn;
        if ($tableName == 'roles') {
            $this->pkColumn = 'role_id';
        }
        if ($tableName == 'users') {
            $this->pkColumn = 'user_id';
        }
        $this->tableName = $tableName;
        $this->db = $db;
        $this->filters = [];
        $this->delete_child = false;
    }

    /**
     * @param string    $tableName
     * @param string    $domain
     * @param CDatabase $db
     * @param type      $prefix
     *
     * @return \CTreeDB
     */
    public static function factory($tableName, $domain = null, $db = null, $prefix = '') {
        return new CTreeDB($tableName, $domain, $db, $prefix);
    }

    public function setDisplayCallback() {
        return $this;
    }

    public function setPkColumn($pkColumn) {
        $this->pkColumn = $pkColumn;

        return $this;
    }

    /**
     * @param int $id
     *
     * @return $this
     */
    public function setOrgId($id) {
        $this->orgId = $id;

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
        $where = '';
        $db = $this->db;
        foreach ($this->filters as $k => $v) {
            if ($v === null) {
                $where .= ' AND ' . $db->escapeColumn($k) . ' is null';
            } else {
                $where .= ' AND ' . $db->escapeColumn($k) . ' = ' . $db->escape($v);
            }
        }
        //if(strlen($where)>0) $where = substr($where,5);
        return $where;
    }

    public function getList($indent = '') {
        $db = $this->db;

        $q = '
			SELECT ' . $db->escapeColumn($this->pkColumn) . ' , CONCAT( REPEAT(' . $db->escape($indent) . ', depth), node.name) AS name
			FROM ' . $db->escapeTable($this->tableName) . ' AS node
			WHERE status>0 ';

        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $q .= '
			' . $this->filterWhere() . '
			ORDER BY node.lft
		';

        return $db->getList($q);
    }

    public function insert($data, $parent_id = null) {
        $db = $this->db;
        if ($parent_id != null) {
            $q = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where  ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($parent_id);
            if (strlen($this->orgId) > 0) {
                $q .= ' and (org_id=' . $db->escape($this->orgId) . ' or org_id is null)';
            }
            $rgt = $db->getValue($q);

            $q = 'select lft from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($parent_id);
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
            }
            $lft = $db->getValue($q);

            $q = 'select depth from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($parent_id);
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
            }
            $depth = $db->getValue($q);

            $q = 'update ' . $db->escapeTable($this->tableName) . ' set lft=lft+2 where status>0 and  lft>' . $db->escape($rgt - 1);
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
            }
            $db->query($q);

            $q = 'update ' . $db->escapeTable($this->tableName) . ' set rgt=rgt+2 where status>0 and  rgt>' . $db->escape($rgt - 1);
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
            }
            $db->query($q);

            $data['parent_id'] = $parent_id;
            $data['lft'] = $rgt;
            $data['rgt'] = $rgt + 1;

            $data['depth'] = $depth + 1;
        } else {
            $q = '
                select
                    max(rgt)
                from
                    ' . $db->escapeTable($this->tableName) . '
                where
                    1=1
            ';
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
            }

            $rgt = $db->getValue($q);

            if ($rgt == null) {
                $rgt = 0;
            }
            $data['lft'] = $rgt + 1;
            $data['rgt'] = $rgt + 2;
            $data['depth'] = 0;
        }

        $inserted = $db->table($this->tableName)->insert($data);

        return $inserted ? $db->getPdo()->lastInsertId() : null;
    }

    public function delete($id) {
        $app = CApp::instance();
        $user = $app->user();

        $db = $this->db;

        $qrgt = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
        if (strlen($this->orgId) > 0) {
            $qrgt .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $qlft = 'select lft from ' . $db->escapeTable($this->tableName) . ' where  ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
        if (strlen($this->orgId) > 0) {
            $qlft .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $rgt = $db->getValue($qrgt);
        $lft = $db->getValue($qlft);
        $width = ($rgt - $lft) + 1;
        $q = 'update ' . $db->escapeTable($this->tableName) . ' set status=0, updated=' . $db->escape(date('Y-m-d H:i:s')) . ',updatedby=' . $db->escape($user->username) . ' where lft between ' . $db->escape($lft) . ' and ' . $db->escape($rgt) . ' ';
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $q .= ' ' . $this->filter_where() . ' ';
        $db->query($q);

        if ($this->delete_child == true) {
            $child_list = $this->getChildrenList($id);
            if (is_array($child_list) && count($child_list) > 0) {
                foreach ($child_list as $child_list_k => $child_list_v) {
                    $q = 'update ' . $db->escapeTable($this->tableName) . ' set status=0, updated=' . $db->escape(date('Y-m-d H:i:s')) . ',updatedby=' . $db->escape($user->username) . ' where ' . $this->pkColumn . '=' . $db->escape($child_list_k);
                    $db->query($q);
                }
            }
        }

        $q = 'update ' . $db->escapeTable($this->tableName) . ' set rgt=rgt-' . $db->escape($width) . ' where status>0 and rgt>' . $db->escape($rgt);
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }

        $db->query($q);
        $q = 'update ' . $db->escapeTable($this->tableName) . ' set lft=lft-' . $db->escape($width) . ' where status>0 and  lft>' . $db->escape($rgt);
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }

        $db->query($q);
    }

    public function update($id, $data, $parent_id) {
        $db = $this->db;

        $db->update($this->tableName, $data, [$this->pkColumn => $id]);
        if ($this->orgId != null) {
            $this->rebuildTreeAll();
        }
    }

    public function getParents($parent_id) {
        $db = $this->db;

        $q = 'SELECT rgt,lft
                FROM ' . $db->escapeTable($this->tableName) . '
                WHERE ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($parent_id);
        $row = $db->getRow($q);
        $rgt = c::get($row, 'rgt');
        $lft = c::get($row, 'lft');

        $q = ' select * from ' . $db->escapeTable($this->tableName) . ' where  status>0 and lft<' . $db->escape($lft) . ' and rgt>' . $db->escape($rgt) . ' ';
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . ' ';
        }
        $q .= $this->filter_where();
        $q .= ' order by depth asc';

        $r = $db->query($q);

        return $r;
    }

    public function getFirstParent($parent_id) {
        $db = $this->db;
        $q = 'SELECT rgt,lft
                FROM ' . $db->escapeTable($this->tableName) . '
                WHERE ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($parent_id);
        $row = $db->getRow($q);
        $rgt = c::get($row, 'rgt');
        $lft = c::get($row, 'lft');

        $q = ' select * from ' . $db->escapeTable($this->tableName) . ' where  status>0 and lft<' . $db->escape($lft) . ' and rgt>' . $db->escape($rgt) . ' ';
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . ' or org_id is null';
        }
        $q .= $this->filter_where();
        $q .= ' order by lft asc limit 1';

        $r = $db->getRow($q);

        return $r;
    }

    public function getChildrenList($id = null, $indent = '') {
        $db = $this->db;

        $q = '
			SELECT ' . $db->escapeColumn($this->pkColumn) . ' , CONCAT( REPEAT(' . $db->escape($indent) . ', depth), node.name) AS name
			FROM ' . $db->escapeTable($this->tableName) . ' AS node
			WHERE status>0

		' . $this->filter_where();

        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $q .= ' ORDER BY node.lft';

        if ($id != null) {
            $qrgt = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
            if (strlen($this->orgId) > 0) {
                $qrgt .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $qlft = 'select lft from ' . $db->escapeTable($this->tableName) . ' where  ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
            if (strlen($this->orgId) > 0) {
                $qlft .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $rgt = $db->getValue($qrgt);
            $lft = $db->getValue($qlft);

            $q = '
				SELECT ' . $db->escapeColumn($this->pkColumn) . ' , CONCAT( REPEAT(' . $db->escape($indent) . ', depth), node.name) AS name
				FROM ' . $db->escapeTable($this->tableName) . ' AS node
				WHERE
					status>0
					and lft>' . $db->escape($lft) . ' and rgt<' . $db->escape($rgt) . '

			' . $this->filter_where();
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $q .= ' ORDER BY node.lft';
        }

        return $db->getList($q);
    }

    public function getChildrenLeafList($id = null) {
        $db = $this->db;

        $q = '
			SELECT ' . $db->escapeColumn($this->pkColumn) . ' ,node.name
			FROM ' . $db->escapeTable($this->tableName) . ' AS node
			WHERE status>0  and rgt=lft+1

		';

        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $q .= $this->filter_where();
        $q .= ' ORDER BY node.lft';

        if ($id != null) {
            $qrgt = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
            if (strlen($this->orgId) > 0) {
                $qrgt .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $qlft = 'select lft from ' . $db->escapeTable($this->tableName) . ' where  ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';
            if (strlen($this->orgId) > 0) {
                $qlft .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $rgt = $db->getValue($qrgt);
            $lft = $db->getValue($qlft);

            $q = '
				SELECT ' . $db->escapeColumn($this->pkColumn) . ' , node.name
				FROM ' . $db->escapeTable($this->tableName) . ' AS node
				WHERE
					status>0
					and lft>' . $db->escape($lft) . ' and rgt<' . $db->escape($rgt) . ' and rgt=lft+1
			';

            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $q .= ' ORDER BY node.lft';
        }
        $list = $db->getList($q);

        return $list;
    }

    public function getChildrenData($id = null) {
        $db = $this->db;
        $q = 'select * from ' . $db->escapeTable($this->tableName) . ' where status>0 ' . $this->filter_where();
        if (strlen($this->orgId) > 0 && $this->orgId != 'ALL' && $this->orgId != 'NONE') {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        } elseif (strlen($this->orgId) > 0 && $this->orgId == 'NONE') {
            $q .= ' and org_id is null ';
        }

        $q .= ' order by lft asc';
        if ($id != null) {
            $qrgt = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';

            $qlft = 'select lft from ' . $db->escapeTable($this->tableName) . ' where  ' . $db->escapeColumn($this->pkColumn) . ' = ' . $db->escape($id) . '';

            $rgt = $db->getValue($qrgt);
            $lft = $db->getValue($qlft);
            $q = 'select * from ' . $db->escapeTable($this->tableName) . ' where status>0 and lft>' . $db->escape($lft) . ' and rgt<' . $db->escape($rgt) . ' ' . $this->filter_where() . ' ';
            if (strlen($this->orgId) > 0) {
                $q .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $q .= ' order by lft asc';
        }
        $r = $db->query($q)->result(false);

        return $r;
    }

    public function rebuildTreeAll($force = false) {
        if (!$force) {
            if ($this->orgId == null) {
                throw new Exception('Service Unavailable on rebuild_tree_all on org_id null');
            }
        }
        $db = $this->db;
        $q = 'select ' . $db->escapeColumn($this->pkColumn) . ' from ' . $db->escapeTable($this->tableName) . ' where status>0';
        if (strlen($this->orgId) > 0) {
            if ($this->orgId == 'NONE') {
                $q .= ' and org_id is null';
            } else {
                $q .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
        }

        $q .= ' and parent_id is null order by lft,' . $db->escapeColumn($this->pkColumn) . ' asc';
        $r = $db->query($q)->result(false);
        $left = 1;

        foreach ($r as $row) {
            $pk = $this->pkColumn;
            $this->rebuildTree($row[$pk], $left);
            //$qleft="select rgt from " . $db->escapeTable($this->tableName) . " where org_id=" . $db->escape($this->orgId) . " and status>0 and " . $db->escapeColumn($this->pkColumn) . "=" . $db->escape($row[$pk]);
            $qleft = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where status>0 and ' . $db->escapeColumn($this->pkColumn) . '=' . $db->escape($row[$pk]);
            if (strlen($this->orgId) > 0) {
                if ($this->orgId == 'NONE') {
                    $qleft .= ' and org_id is null';
                } else {
                    $qleft .= ' and org_id=' . $db->escape($this->orgId) . '';
                }
            }
            $left = $db->getValue($qleft) + 1;
        }
    }

    public function rebuildTreeAllIgnoreStatus() {
        $db = $this->db;
        $q = 'select ' . $db->escapeColumn($this->pkColumn) . ' from ' . $db->escapeTable($this->tableName) . ' where 1=1 ';
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }
        $q .= ' and parent_id is null order by lft asc';
        $r = $db->query($q)->result(false);
        $left = 1;

        foreach ($r as $row) {
            $pk = $this->pkColumn;
            $this->rebuild_tree_ignore_status($row[$pk], $left);
            //$qleft="select rgt from " . $db->escapeTable($this->tableName) . " where org_id=" . $db->escape($this->orgId) . " and status>0 and " . $db->escapeColumn($this->pkColumn) . "=" . $db->escape($row[$pk]);
            $qleft = 'select rgt from ' . $db->escapeTable($this->tableName) . ' where 1=1 and ' . $db->escapeColumn($this->pkColumn) . '=' . $db->escape($row[$pk]);
            if (strlen($this->orgId) > 0) {
                $qleft .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
            $left = $db->getValue($qleft) + 1;
        }
    }

    public function rebuildTree($id = null, $left = 1, $depth = 0) {
        // the right value of this node is the left value + 1
        $db = $this->db;
        $right = $left + 1;
        // get all children of this node
        $q = 'select ' . $db->escapeColumn($this->pkColumn) . ' from ' . $db->escapeTable($this->tableName) . ' where  status>0';
        if (strlen($this->orgId) > 0) {
            if ($this->orgId == 'NONE') {
                $q .= ' and org_id is null';
            } else {
                $q .= ' and org_id=' . $db->escape($this->orgId) . '';
            }
        }

        if ($id != null) {
            $q .= ' and parent_id = ' . $db->escape($id);
        } else {
            $q .= ' and parent_id is null';
        }

        if ($this->have_priority) {
            $q .= ' ORDER BY priority';
        } else {
            $q .= ' ORDER BY lft,' . $db->escapeColumn($this->pkColumn) . ' asc';
        }

        $r = $db->query($q)->result(false);
        foreach ($r as $row) {
            // recursive execution of this function for each
            // child of this node
            // $right is the current right value, which is
            // incremented by the rebuild_tree function
            $pk = $this->pkColumn;
            $right = $this->rebuildTree($row[$pk], $right, $depth + 1);
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value

        $data = [
            'lft' => $left,
            'rgt' => $right,
            'depth' => $depth,
        ];
        $db->update($this->tableName, $data, [$this->pkColumn => $id]);
        // return the right value of this node + 1
        return $right + 1;
    }

    public function rebuildTreeIgnoreStatus($id = null, $left = 1, $depth = 0) {
        // the right value of this node is the left value + 1
        $db = $this->db;
        $right = $left + 1;
        // get all children of this node
        $q = 'select ' . $db->escapeColumn($this->pkColumn) . ' from ' . $db->escapeTable($this->tableName) . ' where 1=1 ';
        if (strlen($this->orgId) > 0) {
            $q .= ' and org_id=' . $db->escape($this->orgId) . '';
        }

        if ($id != null) {
            $q .= ' and parent_id = ' . $db->escape($id);
        } else {
            $q .= ' and parent_id is null';
        }

        if ($this->have_priority) {
            $q .= ' ORDER BY priority';
        } else {
            $q .= ' ORDER BY lft asc';
        }
        $r = $db->query($q)->result(false);
        foreach ($r as $row) {
            // recursive execution of this function for each
            // child of this node
            // $right is the current right value, which is
            // incremented by the rebuild_tree function
            $pk = $this->pkColumn;
            $right = $this->rebuildTree($row[$pk], $right, $depth + 1);
        }

        // we've got the left value, and now that we've processed
        // the children of this node we also know the right value

        $data = [
            'lft' => $left,
            'rgt' => $right,
            'depth' => $depth,
        ];
        $db->update($this->tableName, $data, [$this->pkColumn => $id]);
        // return the right value of this node + 1
        return $right + 1;
    }
}

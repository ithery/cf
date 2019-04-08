<?php

class CJDB {

    public static $instance = null;
    private $path = "";
    protected $select = array();
    protected $set = array();
    protected $from = array();
    protected $join = array();
    protected $where = array();
    protected $orderby = array();
    protected $order = array();
    protected $groupby = array();
    protected $having = array();
    protected $distinct = FALSE;
    protected $limit = FALSE;
    protected $offset = FALSE;

    private function __construct($path) {
        CCollector::deprecated();
        $this->path = $path;
    }

    public static function & instance($path = "") {
        if ($path == "")
            $path = DOCROOT . 'cjdb' . DIRECTORY_SEPARATOR;
        if (self::$instance == null)
            self::$instance = new CJDB($path);
        return self::$instance;
    }

    public function load($table) {
        return CJDBTable::factory($this->path . $table);
    }

    public function insert($table, $data) {
        return $this->load($table)->insert($data)->save();
    }

    public function update($table, $data, $where = null) {
        return $this->load($table)->update($data, $where)->save();
    }

    public function get($table, $where = null) {
        return $this->load($table)->get($where);
    }

    public function delete($table, $where = null) {
        return $this->load($table)->delete($where)->save();
    }

    public function select($table, $column = null, $where = null) {
        return $this->load($table)->select($column, $where);
    }

    public function get_list($table, $key, $value, $where = null) {
        return $this->load($table)->get_list($key, $value, $where);
    }

}

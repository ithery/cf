<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 2, 2019, 10:52:01 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CTrait_Element_Property_TableData {

    /**
     *
     * @var array
     */
    protected $tableData;

    /**
     *
     * @var boolean
     */
    protected $tableDataIsAjax;

    /**
     *
     * @var string
     */
    protected $tableDataQuery;

    /**
     * 
     * @param string $q
     * @return $this
     */
    public function setDataFromQuery($q) {
        $db = CDatabase::instance();
        if ($this->isUseTrait('CTrait_Element_Property_Database')) {
            $db = $this->db();
        }
        if ($this->ajax == false) {

            $r = $db->query($q);
            $this->tableData = $r->resultArray();
        }
        $this->query = $q;
        return $this;
    }

    /**
     * 
     * @param CModel|CModel_Query $model
     * @return $this
     */
    public function setDataFromModel($model) {
        $modelQuery = $model;
        if ($modelQuery instanceof CModel_Collection) {
            throw new CException('error when calling setDataFromModel, please use CModel/CModel_Query instance (CModel_Collection passed)');
        }
        $sql = $this->db->compileBinds($modelQuery->toSql(), $modelQuery->getBindings());
        return $this->setDataFromQuery($sql);
    }

    /**
     * 
     * @param CElastic_Search $el
     * @param string $require
     * @return $this
     */
    public function setDataFromElastic($el, $require = null) {
        $this->query = $el;
        $this->isElastic = true;
        if ($el instanceof CElastic_Search) {

            $this->query = $el->ajax_data();
        }
        return $this;
    }

    /**
     * 
     * @param callable $callback
     * @param array $callbackOptions
     * @param string $require
     * @return $this
     */
    public function setDataFromCallback($callback, $callbackOptions = array(), $require = null) {
        $this->query = $callback;
        $this->isCallback = true;
        $this->callbackOptions = $callbackOptions;
        $this->callbackRequire = $require;

        return $this;
    }

    /**
     * 
     * @param array $array
     * @return $this
     */
    public function setDataFromArray($array) {
        $this->tableData = $array;
        return $this;
    }

    public function setTableDataIsAjax($bool = true) {
        $this->tableDataIsAjax = $bool;
        return $this;
    }

}

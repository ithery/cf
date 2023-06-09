<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 2, 2019, 10:52:01 PM
 */
trait CTrait_Element_Property_TableData {
    /**
     * @var array
     */
    protected $tableData;

    /**
     * @var bool
     */
    protected $tableDataIsAjax;

    /**
     * @var string
     */
    protected $tableDataQuery;

    /**
     * @var array
     */
    protected $tableDataCallbackOptions;

    /**
     * @var string
     */
    protected $tableDataCallbackRequire;

    /**
     * @var string
     */
    protected $tableDataType;

    /**
     * @param string $q
     *
     * @return $this
     */
    public function setDataFromQuery($q) {
        $this->tableDataQuery = $q;
        $this->tableDataType = 'query';

        return $this;
    }

    /**
     * @param CModel|CModel_Query $model
     *
     * @return $this
     */
    public function setDataFromModel($model) {
        $modelQuery = $model;
        if ($modelQuery instanceof CModel_Collection) {
            throw new Exception('error when calling setDataFromModel, please use CModel/CModel_Query instance (CModel_Collection passed)');
        }
        $sql = $this->db->compileBinds($modelQuery->toSql(), $modelQuery->getBindings());
        $this->tableDataType = 'model';

        return $this->setDataFromQuery($sql);
    }

    /**
     * @param CElastic_Search $el
     * @param string          $require
     *
     * @return $this
     */
    public function setDataFromElastic($el, $require = null) {
        $this->query = $el;
        $this->isElastic = true;
        $this->tableDataType = 'elastic';
        if ($el instanceof CElastic_Search) {
            $this->query = $el->ajaxData();
        }

        return $this;
    }

    /**
     * @param callable $callback
     * @param array    $callbackOptions
     * @param string   $require
     *
     * @return $this
     */
    public function setDataFromCallback($callback, $callbackOptions = [], $require = null) {
        $this->tableDataQuery = c::toSerializableClosure($callback);
        $this->tableDataCallbackOptions = $callbackOptions;
        $this->tableDataCallbackRequire = $require;
        $this->tableDataIsAjax = true;
        $this->tableDataType = 'callback';

        return $this;
    }

    /**
     * @param array $array
     *
     * @return $this
     */
    public function setDataFromArray($array) {
        $this->tableData = $array;
        $this->tableDataIsAjax = false;

        return $this;
    }

    public function setTableDataIsAjax($bool = true) {
        $this->tableDataIsAjax = $bool;

        return $this;
    }

    public function populateTableData($data) {
        $this->tableData = carr::get($data, 'tableData');
        $this->tableDataCallbackOptions = carr::get($data, 'tableDataCallbackOptions');
        $this->tableDataCallbackRequire = carr::get($data, 'tableDataCallbackRequire');
        $this->tableDataIsAjax = carr::get($data, 'tableDataIsAjax');
        $this->tableDataQuery = carr::get($data, 'tableDataQuery');
        $this->tableDataType = carr::get($data, 'tableDataType');

        return $this;
    }

    public function getTableDataArray() {
        $data = [];
        $data['tableData'] = $this->tableData;
        $data['tableDataCallbackOptions'] = $this->tableDataCallbackOptions;
        $data['tableDataCallbackRequire'] = $this->tableDataCallbackRequire;
        $data['tableDataIsAjax'] = $this->tableDataIsAjax;
        $data['tableDataQuery'] = $this->tableDataQuery;
        $data['tableDataType'] = $this->tableDataType;

        return $data;
    }

    public function getTableData() {
        switch ($this->tableDataType) {
            case 'query':
                $db = c::db();
                if ($this->isUseTrait('CTrait_Element_Property_Database')) {
                    $db = $this->db();
                }
                $r = $db->query($this->tableDataQuery);

                return $r->resultArray(false);

                break;
            default:
                return $this->tableData;
        }
    }
}

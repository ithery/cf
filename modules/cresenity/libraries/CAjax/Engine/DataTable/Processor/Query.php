<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:18 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Query extends CAjax_Engine_DataTable_Processor {

    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    private $queryWhere;
    private $queryOrderBy;
    private $queryLimit;
    private $baseQuery;
    private $baseOrder;
    private $query;
    private $db;
    private $columns;

    /**
     *
     * @var CElement_Component_DataTable
     */
    private $table;

    protected function db() {
        if ($this->db === null) {
            $domain = carr::get($this->data, 'domain');
            $this->db = CDatabase::instance(null, null, $domain);
        }
        return $this->db;
    }

    protected function getQuery() {
        if ($this->query === null) {
            $this->query = carr::get($this->data, 'query');
        }
        return $this->query;
    }

    protected function getBaseQuery() {
        if ($this->baseQuery === null) {
            $qBase = $this->getQuery();

            $maxWhere = strrpos(strtolower($qBase), 'where');
            $maxFrom = strrpos(strtolower($qBase), 'from');

            $maxOffset = 0;
            if ($maxWhere !== false && $maxWhere > $maxOffset) {
                $maxOffset = $maxWhere;
            }
            if ($maxFrom !== false && $maxFrom > $maxOffset) {
                $maxOffset = $maxFrom;
            }

            $posOrderBy = strrpos(strtolower($qBase), "order by", $maxOffset);

            $postLastBracket = strrpos(strtolower($qBase), ")");

            $stringOrderBy = '';
            if ($posOrderBy !== false && $posOrderBy > $postLastBracket) {

                $stringOrderBy = substr($qBase, $posOrderBy, strlen($qBase) - $posOrderBy);
                $qBase = substr($qBase, 0, $posOrderBy);
            }
            $this->baseQuery = $qBase;
            $this->baseOrder = $stringOrderBy;
        }
        return $this->baseQuery;
    }

    protected function getQueryLimit() {
        if ($this->queryLimit === null) {
            $db = $this->db();
            $request = $this->input;
            $sLimit = "";
            if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
                $sLimit = "LIMIT " . intval($request['iDisplayStart']) . ", " . intval($request['iDisplayLength']);
            }
            $this->queryLimit = $sLimit;
        }
        return $this->queryLimit;
    }

    protected function getQueryOrderBy() {

        if ($this->queryOrderBy === null) {
            $db = $this->db();
            $sOrder = "";
            $columns = $this->columns;
            $table = $this->table;
            $request = $this->engine->getInput();
            if (isset($request['iSortCol_0'])) {
                $sOrder = "ORDER BY  ";
                for ($i = 0; $i < intval($request['iSortingCols']); $i++) {
                    $i2 = 0;
                    if ($table->checkbox) {
                        $i2--;
                    }
                    if ($this->actionLocation() == 'first') {
                        $i2--;
                    }
                    $fieldName = carr::get($columns[intval($request['iSortCol_' . $i]) + $i2], 'fieldname');
                    if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == "true") {
                        $sOrder .= "" . $db->escape_column($fieldName) . " " . $db->escape_str($request['sSortDir_' . $i]) . ", ";
                    }
                }
                $sOrder = substr_replace($sOrder, "", -2);
                if ($sOrder == "ORDER BY") {
                    $sOrder = "";
                }
            }
            if (strlen($sOrder) == 0) {
                $stringOrderBy = $this->baseOrder();

                if (strlen($stringOrderBy) > 0) {
                    //remove prefixed column from order by
                    $sub = explode(",", substr($stringOrderBy, 9));
                    $sOrder = "";
                    $newStringOrderBy = '';
                    foreach ($sub as $val) {
                        $columnNames = explode(".", $val);
                        $columnName = $columnNames[0];
                        if (isset($columnNames[1])) {
                            $columnName = $columnNames[1];
                        }
                        $newStringOrderBy .= ", " . $columnName;
                    }
                    $sOrder = "ORDER BY " . substr($newStringOrderBy, 2);
                }
            }
            $this->queryOrderBy = $sOrder;
        }
        return $sOrder;
    }

    protected function baseOrder() {
        if ($this->baseOrder === null) {
            $this->getBaseQuery();
        }
        return $this->baseOrder;
    }

    protected function getQueryWhere() {

        if ($this->queryWhere === null) {
            $request = $this->engine->getInput();
            $table = $this->table;
            $db = $this->db();
            $qs_condition_str = "";
            $sWhere = '';
            $columns = $this->columns;

            if (isset($request['sSearch']) && $request['sSearch'] != "") {
                for ($i = 0; $i < count($columns); $i++) {
                    $i2 = 0;
                    if ($table->checkbox) {
                        $i2 = 1;
                    }
                    $fieldName = carr::get($columns[$i], 'fieldname');
                    if (isset($request['bSearchable_' . ($i + $i2)]) && $request['bSearchable_' . ($i + $i2)] == "true") {
                        $sWhere .= "`" . $fieldName . "` LIKE '%" . $db->escape_like($request['sSearch']) . "%' OR ";
                    }
                }

                $sWhere = substr_replace($sWhere, "", -3);
            }
            // Quick Search
            $qs_cond = array();
            if (isset($request['dttable_quick_search'])) {
                $qs_cond = json_decode($request['dttable_quick_search'], TRUE);
            }
            if (isset($qs_cond) && count($qs_cond) > 0) {


                foreach ($qs_cond as $qs_cond_k => $qs_cond_v) {
                    $value = $qs_cond_v['value'];
                    $transforms = carr::get($qs_cond_v, 'transforms');
                    if (strlen($transforms) > 0) {
                        $transforms = json_decode($transforms, TRUE);

                        foreach ($transforms as $transforms_k => $transforms_v) {
                            $value = ctransform::{$transforms_v['func']}($value, TRUE);
                        }
                    }

                    $field_name = str_replace('dt_table_qs-', '', $qs_cond_v['name']);
                    $qs_condition_str .= "`" . $field_name . "` LIKE '%" . $db->escape_like($value) . "%' AND ";
                }
                $qs_condition_str = substr_replace($qs_condition_str, "", -4);
                if (strlen(trim($sWhere)) > 0)
                    $sWhere = ' ( ' . $sWhere . ' ) AND ';

                $sWhere .= $qs_condition_str;
            }

            if (strlen($sWhere) > 0) {
                $sWhere = " WHERE ( " . $sWhere . " )";
            }

            /* Individual column filtering */
            for ($i = 0; $i < count($columns); $i++) {
                if (isset($request['bSearchable_' . $i]) && $request['bSearchable_' . $i] == "true" && $request['sSearch_' . $i] != '') {
                    if ($sWhere == "") {
                        $sWhere = "WHERE ";
                    } else {
                        $sWhere .= " AND ";
                    }
                    $sWhere .= "`" . $columns[$i]->fieldname . "` LIKE '%" . $db->escape_like($request['sSearch_' . $i]) . "%' ";
                }
            }
            $this->queryWhere = $sWhere;
        }

        return $this->queryWhere;
    }

    protected function getTotalRecord() {
        $db = $this->db();
        $q = $this->getBaseQuery();
        // get total record
        $qTotal = "select count(*) as cnt from (" . $q . ") as a";
        $rTotal = $db->query($qTotal);
        $totalRecord = 0;
        if ($rTotal->count() > 0) {
            $totalRecord = $rTotal[0]->cnt;
        }
        return $totalRecord;
    }

    protected function getTotalFilteredRecord() {
        $db = $this->db();
        $qBase = $this->getBaseQuery();
        $sWhere = $this->getQueryWhere();
        $qFiltered = "select * from (" . $qBase . ") as a " . $sWhere;

        $qTotalFiltered = "select count(*) as cnt from (" . $qFiltered . ") as a";
        $rTotalFiltered = $db->query($qTotalFiltered);
        $totalFilteredRecord = 0;
        if ($rTotalFiltered->count() > 0) {
            $totalFilteredRecord = $rTotalFiltered[0]->cnt;
        }
        return $totalFilteredRecord;
    }

    public function process() {
        $data = $this->data;
        $table = unserialize(carr::get($data, 'table'));
        $domain = carr::get($data, 'domain', CF::domain());
        $q = carr::get($data, 'query');
        $js = "";
        $input = $this->input;
        $db = $this->db();


        $request = $this->input;

        $columns = carr::get($data, 'columns');
        if ($this->columns == null) {
            $this->columns = $columns;
        }
        if ($this->table == null) {
            $this->table = $table;
        }
        $rowActionList = $table->getRowActionList();
        $key = carr::get($data, 'key_field');

        $qBase = $this->getBaseQuery();

        $totalRecord = $this->getTotalRecord();

        /* Paging */
        $sLimit = $this->getQueryLimit();

        /* Ordering */
        $sOrder = $this->getQueryOrderBy();


        /**
         * Build condition query
         */
        $sWhere = $this->getQueryWhere();


        $totalFilteredRecord = $this->getTotalFilteredRecord();

        if (strlen($sOrder) > 0) {
            //order by from paramter found, reset order by from baseQuery
            $stringOrderBy = '';
        }

        $qProcess = "select * from (" . $qBase . ") as a " . $sWhere . " " . $sOrder . ' ' . $sLimit;


        $resultQ = $db->query($qProcess);


        $data = $resultQ->result(false);

        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalFilteredRecord,
            "aaData" => $this->populateAAData($data, $table, $request, $js),
        );



        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}

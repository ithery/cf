<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CManager_DataProvider_SqlDataProvider
 * @see CManager_DataProvider_ModelDataProvider
 * @see CManager_DataProvider_ClosureDataProvider
 * @since Jul 8, 2018, 2:58:18 AM
 */
class CAjax_Engine_DataTable_Processor_DataProvider extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    public function process() {
        $request = $this->input;
        $table = $this->table();
        $query = $table->getQuery();

        /** @var CManager_Contract_DataProviderInterface $query */
        $query->searchOr($this->getSearchDataOr());
        $query->searchAnd($this->getSearchDataAnd());
        $query->sort($this->getSortData());

        $pageSize = $this->parameter->pageSize();
        $collections = null;
        $totalItem = 0;
        $totalFilteredItem = 0;

        if ($pageSize && $pageSize != '-1') {
            $paginationResult = $query->paginate($this->parameter->pageSize(), ['*'], 'page', $this->parameter->page());
            $collections = $paginationResult->items();
            $totalItem = $paginationResult->total();
            $totalFilteredItem = $totalItem;
            if ($query instanceof CManager_DataProvider_SqlDataProvider) {
                $totalItem = $query->getCountForPagination();
                $totalFilteredItem = $query->getTotalFilteredRecord();
            }
        } else {
            $collections = $query->toEnumerable();
            $totalItem = $collections->count();
            $totalFilteredItem = $totalItem;
        }
        $output = [
            'sEcho' => intval(carr::get($request, 'sEcho')),
            'iTotalRecords' => $totalItem,
            'iTotalDisplayRecords' => $totalFilteredItem,
            'aaData' => $this->populateAAData($collections, $this->table(), $request, $js),
        ];

        $data = [
            'datatable' => $output,
            'js' => base64_encode($js),
        ];

        return $data;
    }

    protected function getSearchDataOr() {
        $request = $this->engine->getInput();
        $table = $this->table;

        $searchData = [];
        $columns = $this->columns();

        if (isset($request['sSearch']) && $request['sSearch'] != '') {
            $table = $this->table();
            $columns = $table->getColumns();

            for ($i = 0; $i < count($columns); $i++) {
                $column = $columns[$i];
                $i2 = 0;
                if ($table->checkbox) {
                    $i2++;
                }
                if ($this->actionLocation() == 'first') {
                    $i2++;
                }
                $fieldName = $column->getFieldname();

                if (isset($request['bSearchable_' . ($i + $i2)]) && $request['bSearchable_' . ($i + $i2)] == 'true') {
                    $searchData[$fieldName] = $request['sSearch'];
                }
            }
        }

        return $searchData;
    }

    protected function getSearchDataAnd() {
        $request = $this->engine->getInput();
        $table = $this->table;

        $searchData = [];
        $columns = $this->columns();

        // Quick Search
        $qsCondition = [];
        if (isset($request['dttable_quick_search'])) {
            $qsCondition = json_decode($request['dttable_quick_search'], true);
        }
        if (is_array($qsCondition) && count($qsCondition) > 0) {
            foreach ($qsCondition as $qsConditionKey => $qsConditionValue) {
                $value = $qsConditionValue['value'];
                $transforms = carr::get($qsConditionValue, 'transforms');
                if (strlen($transforms) > 0) {
                    $transforms = json_decode($transforms, true);
                    if (is_array($transforms)) {
                        foreach ($transforms as $transforms_k => $transforms_v) {
                            $value = ctransform::{$transforms_v['func']}($value, true);
                        }
                    }
                }

                $fieldName = str_replace('dt_table_qs-', '', $qsConditionValue['name']);
                $searchData[$fieldName] = $value;
            }
        }

        return $searchData;
    }

    protected function getSortData() {
        $columns = $this->columns();
        $table = $this->table;
        $request = $this->engine->getInput();
        $sortData = [];
        if (isset($request['iSortCol_0'])) {
            for ($i = 0; $i < intval($request['iSortingCols']); $i++) {
                $i2 = 0;
                if ($table->checkbox) {
                    $i2++;
                }
                if ($this->actionLocation() == 'first' && $this->haveRowAction()) {
                    $i2++;
                }
                if ($request['bSortable_' . intval($request['iSortCol_' . $i])] == 'true') {
                    $column = carr::get($columns, intval($request['iSortCol_' . $i]) - $i2);
                    $sortDirection = $request['sSortDir_' . $i];
                    if ($column) {
                        $fieldName = $column->getFieldname();
                        $sortData[$fieldName] = $sortDirection;
                    }
                }
            }
        }

        return $sortData;
    }

    protected function applyDataProvider(CManager_Contract_DataProviderInterface $dataProvider) {
        $request = $this->engine->getInput();
        $table = $this->table;

        $columns = $this->columns();
        $searchDataAnd = $this->getSearchDataAnd();
        $searchDataOr = $this->getSearchDataOr();
        $sortData = $this->getSortData();
        $dataProvider->searchAnd($searchDataAnd);
        $dataProvider->searchOr($searchDataOr);
        $dataProvider->sort($sortData);
    }
}

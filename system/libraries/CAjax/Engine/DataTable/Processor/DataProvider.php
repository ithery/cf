<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 8, 2018, 2:58:18 AM
 */
class CAjax_Engine_DataTable_Processor_DataProvider extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    public function process() {
        $request = $this->input;
        $table = $this->table();
        $query = $table->getQuery();
        /** @var CManager_Contract_DataProviderInterface $query */
        $query->search($this->getSearchData());
        $query->sort($this->getSortData());
        $perPage = null;
        $page = 1;

        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $perPage = $request['iDisplayLength'];
            $start = intval($request['iDisplayStart']);

            $page = floor($start / $perPage) + 1;
        }
        $paginationResult = $query->paginate($this->parameter->pageSize(), ['*'], 'page', $this->parameter->page());

        $output = [
            'sEcho' => intval(carr::get($request, 'sEcho')),
            'iTotalRecords' => $paginationResult->total(),
            'iTotalDisplayRecords' => $paginationResult->total(),
            'aaData' => $this->populateAAData($paginationResult->items(), $this->table(), $request, $js),
        ];

        $data = [
            'datatable' => $output,
            'js' => base64_encode($js),
        ];

        return $data;
    }

    protected function getSearchData() {
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

                    foreach ($transforms as $transforms_k => $transforms_v) {
                        $value = ctransform::{$transforms_v['func']}($value, true);
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
                if ($this->actionLocation() == 'first') {
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
        $searchData = $this->getSearchData();
        $sortData = $this->getSortData();
        $dataProvider->search($searchData);
        $dataProvider->sort($sortData);
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jul 8, 2018, 2:58:18 AM
 */
class CAjax_Engine_DataTable_Processor_ModelQuery extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;
    use CAjax_Engine_DataTable_Trait_ProcessorQueryTrait;

    public function process() {
        $db = $this->db();

        $request = $this->input;

        $queryUnserialized = $this->table()->getQuery();
        $query = CModel_QuerySerializer::unserialize($queryUnserialized);

        if (isset($request['sSearch']) && $request['sSearch'] != '') {
            $table = $this->table();
            $columns = $table->getColumns();

            $query->where(function ($q) use ($columns, $table, $request) {
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
                        if (strpos($fieldName, '.') !== false) {
                            $fields = explode('.', $fieldName);

                            $field = array_pop($fields);
                            $relation = implode('.', $fields);

                            $q->orWhereHas($relation, function ($q2) use ($request, $field) {
                                $q2->where($field, 'like', '%' . $request['sSearch'] . '%');
                            });
                        } else {
                            $q->orWhere($fieldName, 'like', '%' . $request['sSearch'] . '%');
                        }
                    }
                }
            });

            // $sql = CDatabase::instance()->compileBinds($query->toSql(), $query->getBindings());
            // cdbg::dd($sql);
        }
        $perPage = null;
        $page = 1;

        if (isset($request['iDisplayStart']) && $request['iDisplayLength'] != '-1') {
            $perPage = $request['iDisplayLength'];
            $start = intval($request['iDisplayStart']);

            $page = floor($start / $perPage) + 1;
        }

        $modelCollection = null;
        if ($perPage === null) {
            $modelCollection = $query->get();
            $totalItem = $modelCollection->count();
        } else {
            $models = $query->paginate($perPage, ['*'], 'page', $page);
            $totalItem = $models->total();
            $modelCollection = $models->items();
        }

        $output = [
            'sEcho' => intval(carr::get($request, 'sEcho')),
            'iTotalRecords' => $totalItem,
            'iTotalDisplayRecords' => $totalItem,
            'page' => $page,
            'aaData' => $this->populateAAData($modelCollection, $this->table(), $request, $js),
        ];

        $data = [
            'datatable' => $output,
            'js' => cbase64::encode($js),
        ];

        return $data;
    }
}

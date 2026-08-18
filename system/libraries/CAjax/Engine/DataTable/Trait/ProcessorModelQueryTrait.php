<?php

trait CAjax_Engine_DataTable_Trait_ProcessorModelQueryTrait {
    protected function processQueryWhere(CModel_Query $query) {
        $request = $this->engine->getInput();
        $table = $this->table;

        $columns = $this->columns();

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

            // $sql = c::db()->compileBinds($query->toSql(), $query->getBindings());
            // cdbg::dd($sql);
        }
        // Quick Search
        $qs_cond = [];
        if (isset($request['dttable_quick_search'])) {
            $qs_cond = json_decode($request['dttable_quick_search'], true);
        }
        if (isset($qs_cond) && count($qs_cond) > 0) {
            foreach ($qs_cond as $qs_cond_k => $qs_cond_v) {
                $value = $qs_cond_v['value'];
                $transforms = carr::get($qs_cond_v, 'transforms');
                if (strlen($transforms) > 0) {
                    $transforms = json_decode($transforms, true);

                    foreach ($transforms as $transforms_k => $transforms_v) {
                        $value = ctransform::{$transforms_v['func']}($value, true);
                    }
                }

                $fieldName = str_replace('dt_table_qs-', '', $qs_cond_v['name']);
                if (strpos($fieldName, '.') !== false) {
                    $fields = explode('.', $fieldName);

                    $field = array_pop($fields);
                    $relation = implode('.', $fields);

                    $query->whereHas($relation, function ($q2) use ($value, $field) {
                        $q2->where($field, 'like', '%' . $value . '%');
                    });
                } else {
                    $query->where($fieldName, 'like', '%' . $value . '%');
                }
            }
        }
    }

    protected function processQueryOrder(CModel_Query $query) {
        $columns = $this->columns();
        $table = $this->table;
        $request = $this->engine->getInput();
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
                        if (strpos($fieldName, '.') !== false) {
                            $fields = explode('.', $fieldName);

                            $field = array_pop($fields);
                            $relation = implode('.', $fields);

                            $query->with([$relation => function ($q2) use ($sortDirection, $field) {
                                $q2->orderBy($field, $sortDirection);
                            }]);
                        } else {
                            $query->orderBy($fieldName, $sortDirection);
                        }
                    }
                }
            }
        }
    }

    protected function getFullQuery(CModel_Query $query) {
        $this->processQueryWhere($query);
        $this->processQueryOrder($query);
    }
}

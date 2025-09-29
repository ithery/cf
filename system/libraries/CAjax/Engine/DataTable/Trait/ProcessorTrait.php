<?php
use Carbon\Carbon;

trait CAjax_Engine_DataTable_Trait_ProcessorTrait {
    public function rowGet($row, $field) {
        if ($row instanceof CModel) {
            return $row->$field;
        }

        return carr::get($row, $field);
    }

    public function rowArray($row) {
        if ($row instanceof CModel) {
            return $row->getAttributes();
        }

        return $row;
    }

    public function populateAAData($data, CElement_Component_DataTable $table, $request, &$js) {
        $aaData = [];
        $rowActionList = $table->getRowActionList();
        $no = carr::get($request, 'iDisplayStart', 0);

        foreach ($data as $row) {
            $arr = [];
            $no++;
            $key = carr::get($row, $table->getKeyField(), '');

            $htmlRowAction = '';
            if ($rowActionList != null && $rowActionList->childCount() > 0) {
                $html = new CStringBuilder();

                $html->appendln('<td class="low-padding align-center cell-action td-action ">')->incIndent()->br();

                $jsparam = $this->rowArray($row);

                if (!isset($jsparam['param1'])) {
                    $jsparam['param1'] = $key;
                }

                if ($table->getRowActionList()->getStyle() == 'btn-dropdown') {
                    $table->getRowActionList()
                        ->addClass($table->getActionLocation() == 'first' ? 'dropdown-menu-left' : 'dropdown-menu-right');
                }
                $rowActionList->regenerateId(true);
                $rowActionList->apply('setJsParam', $jsparam);
                $rowActionList->apply('setHandlerParam', $jsparam);

                $actions = $rowActionList->childs();

                foreach ($actions as &$action) {
                    if (($table->filterActionCallbackFunc) != null) {
                        $action->removeClass('d-none');

                        $visibility = CFunction::factory($table->filterActionCallbackFunc)
                            ->addArg($table)
                            ->addArg('action')
                            ->addArg($row)
                            ->addArg($action)
                            ->setRequire($table->requires)
                            ->execute();

                        if ($visibility == false) {
                            $action->addClass('d-none');
                        }
                        $action->setVisibility($visibility);
                    }
                    if ($action instanceof CElement_Component_ActionRow) {
                        $action->applyRowCallback($row);
                    }
                }

                $html->appendln($table->getRowActionList()->html($html->getIndent()));
                $js .= $table->getRowActionList()->js();
                $html->decIndent()->appendln('</td>')->br();
                $htmlRowAction = $html->text();
            }

            if ($table->numbering) {
                $arr[] = $no;
            }

            if ($table->checkbox) {
                $arr[] = $table->callCheckboxRenderer($row);
            }
            if ($table->getActionLocation() == 'first') {
                if ($rowActionList != null && $rowActionList->childCount() > 0) {
                    $arr[] = $htmlRowAction;
                }
            }
            foreach ($table->columns as $col) {
                $cell = new CElement_Component_DataTable_Cell($table, $col, $row);
                $arr[] = $cell->html();
                $js .= $cell->js();
            }
            if ($table->getActionLocation() == 'last') {
                if ($rowActionList != null && $rowActionList->childCount() > 0) {
                    $arr[] = $htmlRowAction;
                }
            }

            $arr['DT_RowId'] = $key;

            if ($table->getRowClassCallbackFunction() != null) {
                $arr['DT_RowClass'] = CFunction::factory($table->getRowClassCallbackFunction())
                    ->addArg($row)
                    ->execute();
            }
            $aaData[] = $arr;
        }

        return $aaData;
    }
}

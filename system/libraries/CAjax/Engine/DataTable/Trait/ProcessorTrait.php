<?php

trait CAjax_Engine_DataTable_Trait_ProcessorTrait {
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
                $jsparam = $row;
                if (!isset($jsparam['param1'])) {
                    $jsparam['param1'] = $key;
                }

                if ($table->getRowActionList()->getStyle() == 'btn-dropdown') {
                    if ($table->getActionLocation() == 'first') {
                        $table->getRowActionList()->addClass('dropdown-menu-left');
                    } else {
                        $table->getRowActionList()->addClass('dropdown-menu-right');
                    }
                }
                $rowActionList->regenerateId(true);
                $rowActionList->apply('setJsParam', $jsparam);

                $rowActionList->apply('setHandlerParam', $jsparam);

                if (($table->filterActionCallbackFunc) != null) {
                    $actions = $rowActionList->childs();

                    foreach ($actions as &$action) {
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

                    //call_user_func($this->cellCallbackFunc,$this,$col->get_fieldname(),$row,$v);
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
                $col_found = false;
                $newValue = '';
                if ($row instanceof CModel) {
                    $fieldName = $col->getFieldname();
                    if (strpos($fieldName, '.') !== false) {
                        $fields = explode('.', $fieldName);
                        $colValue = $row;

                        foreach ($fields as $field) {
                            $colValue = c::optional($colValue)->$field;
                        }
                    } else {
                        $colValue = $row->{$col->getFieldname()};
                    }
                } else {
                    $colValue = carr::get($row, $col->getFieldname());
                }

                $ori_v = $colValue;
                //do transform
                foreach ($col->transforms as $trans) {
                    $colValue = $trans->execute($colValue);
                }

                //if formatted
                if (strlen($col->getFormat()) > 0) {
                    $tempValue = $col->getFormat();
                    foreach ($row as $k2 => $v2) {
                        if (strpos($tempValue, '{' . $k2 . '}') !== false) {
                            $tempValue = str_replace('{' . $k2 . '}', $v2, $tempValue);
                        }
                        $colValue = $tempValue;
                    }
                }
                //if have callback
                if ($col->callback != null) {
                    $colValue = CFunction::factory($col->callback)
                            //->addArg($table)
                        ->addArg($row)
                        ->addArg($colValue)
                        ->setRequire($col->callbackRequire)
                        ->execute();
                    list($colValue, $jsCell) = $this->getHtmlJsCell($colValue);
                    $js .= $jsCell;
                }
                $newValue = $colValue;

                if (($table->cellCallbackFunc) != null) {
                    $newValue = CFunction::factory($table->cellCallbackFunc)
                        ->addArg($table)
                        ->addArg($col->getFieldname())
                        ->addArg($row)
                        ->addArg($newValue)
                        ->setRequire($table->requires)
                        ->execute();

                    if (is_array($newValue) && isset($newValue['html'], $newValue['js'])) {
                        $js .= $newValue['js'];
                        $newValue = $newValue['html'];
                    }
                }
                $class = $col->getClassAttribute();
                switch ($col->getAlign()) {
                    case CConstant::ALIGN_LEFT:
                        $class .= ' align-left';

                        break;
                    case CConstant::ALIGN_RIGHT:
                        $class .= ' align-right';

                        break;
                    case CConstant::ALIGN_CENTER:
                        $class .= ' align-center';

                        break;
                }
                $arr[] = $newValue;
            }
            if ($table->getActionLocation() == 'last') {
                if ($rowActionList != null && $rowActionList->childCount() > 0) {
                    $arr[] = $htmlRowAction;
                }
            }

            $arr['DT_RowId'] = $key;
            $aaData[] = $arr;
        }

        return $aaData;
    }

    protected function getHtmlJsCell($cell) {
        $html = '';
        $js = '';

        if (is_string($cell)) {
            $html = $cell;
        }

        if ($cell instanceof CRenderable) {
            $html = $cell->html();
            $js = $cell->js();
        }

        if (carr::accessible($cell)) {
            $html = carr::get($cell, 'html');
            $js = carr::get($cell, 'js');
        }

        return [$html, $js];
    }
}

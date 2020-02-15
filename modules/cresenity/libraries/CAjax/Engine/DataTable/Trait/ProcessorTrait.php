<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

trait CAjax_Engine_DataTable_Trait_ProcessorTrait {

    public function populateAAData($data, $table, $request, &$js) {
        $aaData = array();
        $rowActionList = $table->getRowActionList();
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = carr::get($row, $table->key_field, '');

            $htmlRowAction = '';
            if ($rowActionList != null && $rowActionList->childCount() > 0) {
               
                $html = new CStringBuilder();

                $html->appendln('<td class="low-padding align-center cell-action td-action ">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->getRowActionList()->getStyle() == "btn-dropdown") {
                    $table->getRowActionList()->add_class("pull-right");
                }
                $rowActionList->regenerateId(true);
                $rowActionList->apply("setJsParam", $jsparam);

                $rowActionList->apply("setHandlerUrlParam", $jsparam);

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
                $arr[] = '<input type="checkbox" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $key . '" value="' . $key . '" class="checkbox-' . $table->id() . '">';
            }
            if ($table->getActionLocation() == 'first') {
                if ($rowActionList != null && $rowActionList->childCount() > 0) {


                    $arr[] = $htmlRowAction;
                }
            }
            foreach ($table->columns as $col) {
                $col_found = false;
                $new_v = "";
                $col_v = carr::get($row, $col->getFieldname());
                $ori_v = $col_v;
                //do transform
                foreach ($col->transforms as $trans) {
                    $col_v = $trans->execute($col_v);
                }

                //if formatted
                if (strlen($col->getFormat()) > 0) {
                    $temp_v = $col->getFormat();
                    foreach ($row as $k2 => $v2) {
                        if (strpos($temp_v, "{" . $k2 . "}") !== false) {
                            $temp_v = str_replace("{" . $k2 . "}", $v2, $temp_v);
                        }
                        $col_v = $temp_v;
                    }
                }
                //if have callback
                if ($col->callback != null) {
                    $col_v = CFunction::factory($col->callback)
                            //->addArg($table)
                            ->addArg($row)
                            ->addArg($col_v)
                            ->setRequire($col->callbackRequire)
                            ->execute();
                    if (is_array($col_v) && isset($col_v['html']) && isset($col_v['js'])) {
                        $js .= $col_v['js'];
                        $col_v = $col_v['html'];
                    }

                }
                $new_v = $col_v;

                if (($table->cellCallbackFunc) != null) {
                    $new_v = CFunction::factory($table->cellCallbackFunc)
                            ->addArg($table)
                            ->addArg($col->getFieldname())
                            ->addArg($row)
                            ->addArg($new_v)
                            ->setRequire($table->requires)
                            ->execute();

                    if (is_array($new_v) && isset($new_v['html']) && isset($new_v['js'])) {
                        $js .= $new_v['js'];
                        $new_v = $new_v['html'];
                    }
                }
                $class = "";
                switch ($col->getAlign()) {
                    case CConstant::ALIGN_LEFT:
                        $class .= " align-left";
                        break;
                    case CConstant::ALIGN_RIGHT:
                        $class .= " align-right";
                        break;
                    case CConstant::ALIGN_CENTER:
                        $class .= " align-center";
                        break;
                }
                $arr[] = $new_v;
            }
            if ($table->getActionLocation() == 'last') {
                if ($rowActionList != null && $rowActionList->childCount() > 0) {


                    $arr[] = $htmlRowAction;
                }
            }

            $arr["DT_RowId"] = $key;
            $aaData[] = $arr;
        }
        return $aaData;
    }

}

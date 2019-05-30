<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Callback extends CAjax_Engine_DataTable_Processor {

    public function process() {
        $data = $this->engine->getData();

        $table = carr::get($data, 'table');
        $keyField = carr::get($data, 'key_field');
        $table = unserialize($table);
        $request = $this->input;
        $callbackRequire = carr::get($data, 'callback_require');
        if (strlen($callbackRequire) > 0 && is_file($callbackRequire)) {
            require_once $callbackRequire;
        }
        $callback = carr::get($data, 'query');
        $params = array();
        $params['options'] = carr::get($data, 'callback_options');
        $resultCallback = call_user_func_array($callback, $params);

        $data = carr::get($resultCallback, 'data', array());
        $totalRecord = carr::get($resultCallback, 'total_record', count($data));
        $totalFilteredRecord = carr::get($resultCallback, 'total_filtered_record', count($data));

        
        $js = '';
        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalFilteredRecord,
            "aaData" => array(),
        );
        $no = carr::get($request, 'iDisplayStart', 0);
        foreach ($data as $row) {
            $arr = array();
            $no++;
            $key = carr::get($row, $table->key_field, '');


            if ($table->numbering) {
                $arr[] = $no;
            }

            if ($table->checkbox) {
                $arr[] = '<input type="checkbox" name="' . $table->id() . '-check[]" id="' . $table->id() . '-' . $key . '" value="' . $key . '" class="checkbox-' . $table->id() . '">';
            }
            foreach ($table->columns as $col) {
                $col_found = false;
                $new_v = "";
                $col_v = "";
                $ori_v = "";
                //do print from query
                foreach ($row as $k => $v) {
                    if ($k == $col->getFieldname()) {
                        $col_v = $v;
                        $ori_v = $col_v;
                        foreach ($col->transforms as $trans) {
                            $col_v = $trans->execute($col_v);
                        }
                    }
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
                                ->addArg($col->getFieldname())
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

                $arr[] = $html->text();
                $arr["DT_RowId"] = $key;
            }
            $output["aaData"][] = $arr;
        }



        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}

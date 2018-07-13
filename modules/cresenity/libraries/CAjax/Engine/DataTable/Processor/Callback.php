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

        $tableData = carr::get($resultCallback, 'data', array());
        $totalRecord = carr::get($resultCallback, 'total_record', count($tableData));
        $totalFilteredRecord = carr::get($resultCallback, 'total_filtered_record', count($tableData));

        $rowActionList = $table->getRowActionList();
        $js = '';
        if (count($rowActionList) > 0) {
            foreach ($tableData as $kRow => $row) {
                $html = new CStringBuilder();
                $key = carr::get($row, $keyField);
                $html->appendln('<td class="low-padding align-center cell-action td-action">')->inc_indent()->br();
                foreach ($row as $k => $v) {
                    $jsparam[$k] = $v;
                }
                $jsparam["param1"] = $key;
                if ($table->getRowActionList()->getStyle() == "btn-dropdown") {
                    $table->getRowActionList()->add_class("pull-right");
                }
                $rowActionList->regenerateId(true);
                $rowActionList->apply("jsparam", $jsparam);

                $rowActionList->apply("set_handler_url_param", $jsparam);

                if (($table->filter_action_callback_func) != null) {
                    $actions = $rowActionList->childs();

                    foreach ($actions as $action) {
                        $visibility = CDynFunction::factory($table->filter_action_callback_func)
                                ->add_param($table)
                                ->add_param($col->get_fieldname())
                                ->add_param($row)
                                ->add_param($action)
                                ->set_require($table->requires)
                                ->execute();

                        $action->set_visibility($visibility);
                    }
                }

                $html->appendln($table->getRowActionList()->html($html->get_indent()));
                $js .= $table->getRowActionList()->js();
                $html->decIndent()->appendln('</td>')->br();
                //$arr[] = '';
                $tableData[$kRow][] = $html->text();
                $tableData[$kRow]["DT_RowId"] = $key;
            }
        }



        $output = array(
            "sEcho" => intval(carr::get($request, 'sEcho')),
            "iTotalRecords" => $totalRecord,
            "iTotalDisplayRecords" => $totalFilteredRecord,
            "aaData" => $tableData,
        );
        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}

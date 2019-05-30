<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jul 8, 2018, 2:58:36 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAjax_Engine_DataTable_Processor_Callback extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;
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
            "aaData" => $this->populateAAData($table,$request),
        );
        



        $data = array(
            "datatable" => $output,
            "js" => cbase64::encode($js),
        );
        $response = json_encode($data);
        return $response;
    }

}

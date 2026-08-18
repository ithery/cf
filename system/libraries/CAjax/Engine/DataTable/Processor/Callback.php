<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_DataTable_Processor_Callback extends CAjax_Engine_DataTable_Processor {
    use CAjax_Engine_DataTable_Trait_ProcessorTrait;

    public function process() {
        $data = $this->engine->getData();

        $table = carr::get($data, 'table');
        $keyField = carr::get($data, 'keyField');
        $table = unserialize($table);
        $request = $this->input;
        $callbackRequire = carr::get($data, 'callbackRequire');
        if (strlen($callbackRequire) > 0 && is_file($callbackRequire)) {
            require_once $callbackRequire;
        }
        $callback = carr::get($data, 'query');
        $params = [];
        $params[] = carr::get($data, 'callbackOptions');
        $params[] = $this;

        $resultCallback = call_user_func_array($callback, $params);

        $data = carr::get($resultCallback, 'data', []);

        $totalRecord = carr::get($resultCallback, 'total_record', is_array($data) ? count($data) : 0);
        $totalFilteredRecord = carr::get($resultCallback, 'total_filtered_record', is_array($data) ? count($data) : 0);

        $js = '';
        $output = [
            'sEcho' => intval(carr::get($request, 'sEcho')),
            'iTotalRecords' => $totalRecord,
            'iTotalDisplayRecords' => $totalFilteredRecord,
            'aaData' => $this->populateAAData($data, $table, $request, $js),
        ];

        $data = [
            'datatable' => $output,
            'js' => base64_encode($js),
        ];

        return $data;
    }
}

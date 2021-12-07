<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2018, 11:06:04 PM
 */
class CAjax_Engine_DataTable extends CAjax_Engine {
    public function createProcessor($type) {
        $class = 'CAjax_Engine_DataTable_Processor_' . $type;

        return new $class($this);
    }

    public function execute() {
        $data = $this->ajaxMethod->getData();

        $isElastic = carr::get($data, 'isElastic');
        $isCallback = carr::get($data, 'isCallback');
        $isModelQuery = carr::get($data, 'isModelQuery');
        $isDataProvider = carr::get($data, 'isDataProvider');

        $processorType = 'Query';
        if ($isElastic) {
            $processorType = 'Elastic';
        }
        if ($isCallback) {
            $processorType = 'Callback';
        }
        if ($isModelQuery) {
            $processorType = 'ModelQuery';
        }
        if ($isDataProvider) {
            $processorType = 'DataProvider';
        }
        $processor = $this->createProcessor($processorType, $data);

        $response = $processor->process();

        if (is_array($response)) {
            return c::response()->json($response);
        }

        return $response;
    }
}

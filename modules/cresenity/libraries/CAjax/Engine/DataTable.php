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

        $processorType = 'Query';
        if ($isElastic) {
            $processorType = 'Elastic';
        }
        if ($isCallback) {
            $processorType = 'Callback';
        }
        $processor = $this->createProcessor($processorType, $data);

        $response = $processor->process();

        if (is_array($response)) {
            return c::response()->json($response);
        }
        return $response;
    }
}

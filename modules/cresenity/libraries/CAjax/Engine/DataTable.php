<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 11:06:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
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

        return $response;
    }

}

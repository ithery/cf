<?php
class CAjax_Engine_DataTableExporter extends CAjax_Engine {
    public function createProcessor($type) {
        $class = 'CAjax_Engine_DataTable_ExporterProcessor_' . $type;
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

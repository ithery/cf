<?php
class CAjax_Engine_DataTableExporter extends CAjax_Engine {
    /**
     * @param string $processorClass
     *
     * @return CAjax_Engine_DataTable_Processor
     */
    public function createProcessor($processorClass) {
        return new $processorClass($this);
    }

    public function execute() {
        $data = $this->ajaxMethod->getData();

        $isElastic = carr::get($data, 'isElastic');
        $isCallback = carr::get($data, 'isCallback');

        $processorClass = CAjax_Engine_DataTable_ExporterProcessor_Query::class;

        $processor = $this->createProcessor($processorClass);
        $response = $processor->process();

        return $response;
    }
}

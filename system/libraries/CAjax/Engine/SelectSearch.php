<?php

defined('SYSPATH') or die('No direct access allowed.');

class CAjax_Engine_SelectSearch extends CAjax_Engine {
    /**
     * @param string $class
     *
     * @return CAjax_Engine_SelectSearch_Processor
     */
    public function createProcessor($class) {
        return new $class($this);
    }

    public function execute() {
        $input = $this->input;
        $data = $this->ajaxMethod->getData();

        $processorClass = CAjax_Engine_SelectSearch_Processor_Query::class;
        $dataProvider = carr::get($data, 'dataProvider');

        if ($dataProvider != null) {
            $dataProvider = unserialize($dataProvider);
            if ($dataProvider instanceof CManager_Contract_DataProviderInterface) {
                $processorClass = CAjax_Engine_SelectSearch_Processor_DataProvider::class;
            }
        }

        $processor = $this->createProcessor($processorClass);
        $response = $processor->process();

        return $response;
    }
}

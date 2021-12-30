<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 15, 2018, 12:46:23 AM
 */
class CAjax_Engine_SearchSelect extends CAjax_Engine {
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

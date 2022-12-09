<?php

class CMetric_Driver_InfluxDBDriver_FluxQueryResult {
    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function toQueryResult() {
        $tableData = [];
        $arrayFluxTable = carr::wrap($this->result);

        foreach ($arrayFluxTable as $table) {
            /** @var \InfluxDB2\FluxTable $fluxTable */
            // *** Column search right here! ****
            $resultKey = array_search('result', array_column($table->columns, 'label'));
            $name = $table->columns[$resultKey]->defaultValue;

            foreach ($table->records as $record) {
                $tableData[$name][$record->getTime()][$record->getField()] = $record->getValue();
            }
        }
        cdbg::dd($tableData);
    }
}

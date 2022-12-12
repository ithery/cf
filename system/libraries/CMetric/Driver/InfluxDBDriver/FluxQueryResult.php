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
            // $resultKey = array_search('result', array_column($table->columns, 'label'));
            // $name = $table->columns[$resultKey]->defaultValue;

            foreach ($table->records as $record) {
                // if (!isset($tableData[$record->getTime()])) {
                //     $tableData[$record->getTime()] = [];
                //     //$tableData[$record->getTime()]['_time'] = $record->getTime();
                // }
                $tableData[$record->getTime()][$record->getField()] = $record->getValue();
                $valueKeys = array_keys($record->values);
                for ($i = 0; $i < count($valueKeys); $i++) {
                    $tableData[$record->getTime()][$valueKeys[$i]] = $record->values[$valueKeys[$i]];
                }
            }
        }

        return new CMetric_QueryResult($tableData);
    }
}

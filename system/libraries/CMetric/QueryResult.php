<?php

class CMetric_QueryResult {
    protected $data;

    public function __construct($data) {
        $this->data = $data;
    }

    public function getData() {
        return $this->data;
    }

    public function all() {
        foreach ($this->data as $table) {
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
                // $valueKeys = array_keys($record->values);
                // for ($i = 0; $i < count($valueKeys); $i++) {
                //     $tableData[$record->getTime()][$valueKeys[$i]] = $record->values[$valueKeys[$i]];
                // }
            }
        }

        return $tableData;
    }
}

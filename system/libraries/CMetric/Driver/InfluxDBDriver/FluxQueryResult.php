<?php

class CMetric_Driver_InfluxDBDriver_FluxQueryResult {
    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function toQueryResult() {
        $tableData = [];
        $arrayFluxTable = carr::wrap($this->result);

        return new CMetric_QueryResult($arrayFluxTable);
    }
}

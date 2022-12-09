<?php

class CMetric_Driver_InfluxDBDriver_FluxQueryResult {
    private $result;

    public function __construct($result) {
        $this->result = $result;
    }

    public function toQueryResult() {
        $tableData = [];
        $arrayFluxTable = carr::wrap($this->result);
        $totalTable = count($arrayFluxTable);
        foreach ($arrayFluxTable as $fluxTable) {
            /** @var \InfluxDB2\FluxTable $fluxTable */
            //cdbg::dd($fluxTable->records[6]);
            cdbg::dd($fluxTable->records[5]);
        }
    }
}

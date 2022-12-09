<?php

class CMetric_Driver_InfluxDBDriver_FluxFormatter {
    /**
     * @var CMetric_QueryBuilder
     */
    protected $query;

    public function __construct(CMetric_QueryBuilder $query) {
        $this->query = $query;
    }

    public function toFluxString() {
        $fluxString = '';
        $fluxString .= $this->formatFrom($this->query->getFrom()) . PHP_EOL;
        $fluxString .= '  |> ' . $this->formatRange($this->query->getPeriod()) . PHP_EOL;

        return $fluxString;
    }

    private function formatFrom($from) {
        return 'from(bucket: "' . $from . '")';
    }

    private function formatRange(CPeriod $period) {
        $start = $period->startDate->toIso8601String();
        $stop = $period->endDate->toIso8601String();

        return 'range(start: ' . $start . ', stop: ' . $stop . ')';
    }
}

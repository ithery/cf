<?php
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;

class CAnalytics_Google_AnalyticGA4_ReportRunner {
    private $dateRanges = [];

    private $metrics = [];

    private $dimensions = [];

    private $report;

    public function __construct() {
    }

    public function setDateRanges(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        $this->dateRanges = new DateRange(
            [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        );

        return $this;
    }

    public function setMetrics(array $metrics = []) {
        $this->metrics = [];

        foreach ($metrics as $metric) {
            $this->metrics[] = new Metric(
                [
                    'name' => $metric,
                ]
            );
        }

        return $this;
    }

    public function setDimensions(array $dimensions = []) {
        $this->dimensions = [];

        foreach ($dimensions as $dimension) {
            $this->dimensions[] = new Dimension(
                [
                    'name' => $dimension,
                ]
            );
        }

        return $this;
    }

    /**
     * Call the query method on the authenticated client.
     */
    public function runReport() {
        $this->report = $this->client->runReport(
            $this->propertyId,
            $this->dateRanges,
            $this->metrics,
            $this->dimensions,
        );

        return $this;
    }

    /**
     * @return \Google\Analytics\Data\V1beta\RunReportResponse
     */
    public function raw() {
        return $this->report;
    }

    public function toArray() {
        $data = [];

        foreach ($this->report->getRows() as $row) {
            if (count($this->dimensions) === 1) {
                $data[$row->getDimensionValues()[0]->getValue()] = c::collect($row->getMetricValues() ?? [])->map(
                    function ($value) {
                        return $value->getValue();
                    }
                );
            } else {
                $dimensions = c::collect($row->getDimensionValues() ?: [])->map(
                    function ($value) {
                        return $value->getValue();
                    }
                );
                $metrics = c::collect($row->getMetricValues() ?: [])->map(
                    function ($value) {
                        return $value->getValue();
                    }
                );

                $data[] = $dimensions->merge($metrics);
            }
        }

        return $data;
    }
}

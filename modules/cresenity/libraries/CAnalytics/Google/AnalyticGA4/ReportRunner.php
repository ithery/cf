<?php
use Google\Analytics\Data\V1beta\Row;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\MetricValue;
use Google\Analytics\Data\V1beta\MinuteRange;
use Google\Analytics\Data\V1beta\MetricHeader;
use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\DimensionHeader;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Analytics\Data\V1beta\DimensionExpression;

class CAnalytics_Google_AnalyticGA4_ReportRunner {
    /**
     * @var array[\Google\Analytics\Data\V1beta\DateRange]
     */
    private $dateRanges = [];

    private $metrics = [];

    private $dimensions = [];

    private $offset = 0;

    private $limit = null;

    /**
     * @var array[\Google\Analytics\Data\V1beta\MinuteRange]
     */
    private $minuteRange = [];

    /**
     * @var \Google\Analytics\Data\V1beta\RunReportResponse
     */
    private $report;

    /**
     * @var CAnalytics_Google_AnalyticGA4_FilterBuilder
     */
    private $dimensionFilter;

    private $metricFilter;

    /**
     * @var CAnalytics_Google_ClientGA4
     */
    private $client;

    /**
     * @var string
     */
    private $propertyId;

    public function __construct(CAnalytics_Google_ClientGA4 $client, $propertyId) {
        $this->client = $client;
        $this->propertyId = $propertyId;
    }

    public function dimensionFilter() {
        if ($this->dimensionFilter == null) {
            $this->dimensionFilter = new CAnalytics_Google_AnalyticGA4_FilterBuilder();
        }

        return $this->dimensionFilter;
    }

    public function withFilterDimension($callback) {
        return c::tap($this->dimensionFilter(), $callback);
    }

    /**
     * @param CPeriod $period
     *
     * @return $this
     */
    public function setPeriod(CPeriod $period) {
        return $this->setDateRanges($period->startDate, $period->endDate);
    }

    /**
     * @param int $startMinutesAgo
     * @param int $endMinutesAgo
     *
     * @return $this
     */
    public function setMinuteRange($startMinutesAgo, $endMinutesAgo = 0) {
        $this->minuteRange = [new MinuteRange([
            'start_minutes_ago' => $startMinutesAgo,
            'end_minutes_ago' => $endMinutesAgo,

        ])];

        return $this;
    }

    /**
     * @param DateTimeInterface $startDate
     * @param DateTimeInterface $endDate
     *
     * @return $this
     */
    public function setDateRanges(DateTimeInterface $startDate, DateTimeInterface $endDate) {
        $this->dateRanges = [new DateRange(
            [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ]
        )];

        return $this;
    }

    /**
     * @param array $metrics
     *
     * @return $this
     */
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

    /**
     * @param array $dimensions
     *
     * @return $this
     */
    public function setDimensions(array $dimensions = []) {
        $this->dimensions = [];

        foreach ($dimensions as $dimension) {
            $this->dimensions[] = new Dimension(['name' => $dimension]);
        }

        return $this;
    }

    /**
     * @param int $offset
     *
     * @return $this
     */
    public function setOffset($offset) {
        $this->offset = $offset;

        return $this;
    }

    /**
     * @param int $limit
     *
     * @return $this
     */
    public function setLimit($limit) {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param mixed      $realtime
     * @param null|mixed $cacheInMinutes
     *
     * @return $this
     */
    public function runReport($cacheInMinutes = null, $realtime = false) {
        $data = [];
        $data['property'] = 'properties/' . $this->propertyId;
        $data['offset'] = $this->offset;
        if ($this->limit) {
            $data['limit'] = $this->limit;
        }
        if ($this->dateRanges && count($this->dateRanges) > 0) {
            $data['dateRanges'] = $this->dateRanges;
        }
        if ($this->dimensions) {
            $data['dimensions'] = $this->dimensions;
        }
        if ($this->metrics) {
            $data['metrics'] = $this->metrics;
        }
        if ($this->minuteRange) {
            $data['minuteRanges'] = $this->minuteRange;
        }

        if ($this->dimensionFilter) {
            $data['dimensionFilter'] = $this->dimensionFilter->toGA4Object();
        }

        $this->report = $this->client->runReport($data, $realtime, $cacheInMinutes);

        return $this;
    }

    public function runReportRealtime($cacheInMinutes = null) {
        return $this->runReport($cacheInMinutes, true);
    }

    /**
     * @return \Google\Analytics\Data\V1beta\RunReportResponse
     */
    public function raw() {
        return $this->report;
    }

    public function toArray() {
        $dimensionHeaders = $this->report->getDimensionHeaders();
        $metricHeaders = $this->report->getMetricHeaders();
        $data = c::collect($this->report->getRows() ?: [])->map(function (Row $row) use ($dimensionHeaders, $metricHeaders) {
            return [
                'dimensions' => c::collect($row->getDimensionValues() ?: [])->mapWithKeys(function (DimensionValue $dimensionValue, $index) use ($dimensionHeaders) {
                    /** @var DimensionHeader $dimensionHeader */
                    $dimensionHeader = $dimensionHeaders[$index];

                    return [$dimensionHeader->getName() => [
                        'name' => $dimensionHeader->getName(),
                        'value' => $dimensionValue->getValue(),
                    ]];
                })->toArray(),
                'metrics' => c::collect($row->getMetricValues() ?: [])->mapWithKeys(function (MetricValue $metricValue, $index) use ($metricHeaders) {
                    /** @var MetricHeader $metricHeader */
                    $metricHeader = $metricHeaders[$index];
                    $typeEnum = $metricHeader->getType();
                    $type = \Google\Analytics\Data\V1beta\MetricType::name($typeEnum);

                    return [$metricHeader->getName() => [
                        'name' => $metricHeader->getName(),
                        'type' => $type,
                        'typeEnum' => $typeEnum,
                        'value' => $metricValue->getValue(),
                    ]];
                })->toArray()
            ];
        })->toArray();

        return $data;
    }

    public function toArray2() {
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

    public function toCollection() {
        return c::collect($this->toArray());
    }
}

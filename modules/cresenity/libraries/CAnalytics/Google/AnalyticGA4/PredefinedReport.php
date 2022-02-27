<?php
use Google\Analytics\Data\V1beta\Row;
use Google\Analytics\Data\V1beta\Metric;
use Google\Analytics\Data\V1beta\DateRange;
use Google\Analytics\Data\V1beta\Dimension;
use Google\Analytics\Data\V1beta\MetricValue;
use Google\Analytics\Data\V1beta\MetricHeader;
use Google\Analytics\Data\V1beta\DimensionValue;
use Google\Analytics\Data\V1beta\DimensionHeader;
use Google\Analytics\Data\V1beta\FilterExpression;
use Google\Analytics\Data\V1beta\RunReportResponse;
use Google\Analytics\Data\V1beta\DimensionExpression;

class CAnalytics_Google_AnalyticGA4_PredefinedReport {
    protected $analytic;

    public function __construct(CAnalytics_Google_AnalyticGA4 $analytic) {
        $this->analytic = $analytic;
    }

    public function getUserActiveCountRealtime($minutes = 29) {
        $analytic = $this->analytic;

        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReport(true)
            ->toArray();
        $activeUserCount = carr::get($data, '0.metrics.activeUsers.value', 0);

        return $activeUserCount;
    }

    public function getUserActivePerMinuteChartData($minutes = 29) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions([$analytic->metadata()->realtimeSchema()->dimension()->minutesAgo()])
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReport(true)
            ->toArray();
        $labels = [];
        $values = [];
        foreach (range(0, 29) as $index) {
            $labels[$index] = str_pad($index, 2, '0', STR_PAD_LEFT);
            $values[$index] = 0;
        }
        foreach ($data as $row) {
            $indexValue = carr::get($row, 'dimensions.minutesAgo.value');
            $index = array_search($indexValue, $labels);
            if ($index !== false) {
                $values[$index] = (int) carr::get($row, 'metrics.activeUsers.value');
            }
        }

        return [
            'labels' => $labels,
            'values' => $values,

        ];
    }
}

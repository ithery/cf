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

    public function getUserActiveCountRealtime() {
        $analytic = $this->analytic;

        $data = $analytic->createReport()
            ->setMinuteRange(5, 0)
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReport(true)
            ->toArray();
        $activeUserCount = carr::get($data, '0.metrics.activeUsers.value', 0);

        return $activeUserCount;
    }

    public function getUserActivePerMinute($minutes = 29) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions([$analytic->metadata()->realtimeSchema()->dimension()->minutesAgo()])
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReport(true)
            ->toArray();

        return $data;
    }
}

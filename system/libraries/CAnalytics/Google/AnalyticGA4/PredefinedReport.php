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

    public function getUserActiveCountRealtime($minutes = 29, $cacheInMinutes = null) {
        $analytic = $this->analytic;

        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReportRealtime($cacheInMinutes)
            ->toArray();
        $activeUserCount = carr::get($data, '0.metrics.activeUsers.value', 0);

        return $activeUserCount;
    }

    public function getUserActivePerMinuteRealtimeChartData($minutes = 29, $cacheInMinutes = null) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions([$analytic->metadata()->realtimeSchema()->dimension()->minutesAgo()])
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReportRealtime($cacheInMinutes)
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

    public function getUserActiveByDeviceCategoryRealtimeChartData($minutes = 29, $cacheInMinutes = null) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions([$analytic->metadata()->realtimeSchema()->dimension()->deviceCategory()])
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReportRealtime($cacheInMinutes)
            ->toArray();
        $labels = [];
        $values = [];

        foreach ($data as $row) {
            $labels[] = carr::get($row, 'dimensions.deviceCategory.value');
            $values[] = (int) carr::get($row, 'metrics.activeUsers.value');
        }

        return [
            'labels' => $labels,
            'values' => $values,

        ];
    }

    /**
     * Peringkat sebuah dimensi realtime, terurut dari yang terbanyak.
     *
     * Satu pintu untuk seluruh rincian yang ditampilkan dashboard realtime —
     * halaman, negara, kota, peristiwa — karena bentuk permintaannya sama
     * persis dan hanya nama dimensi dan metriknya yang berbeda. Menambah
     * rincian baru berarti memanggil ini dengan nama lain, bukan menyalin satu
     * method lagi.
     *
     * Google mengembalikannya sudah terurut menurun, tetapi urutannya
     * dipastikan lagi di sini: yang dipakai pemanggil adalah "sepuluh
     * teratas", dan itu tidak boleh bergantung pada janji yang tidak tertulis.
     *
     * @param string      $dimension      nama dimensi realtime GA4, mis. `unifiedScreenName`, `country`
     * @param string      $metric         nama metrik realtime, mis. `activeUsers`, `screenPageViews`
     * @param int         $limit          banyaknya baris teratas
     * @param int         $minutes
     * @param null|int    $cacheInMinutes
     *
     * @return array tiap entri: label, value
     */
    public function getTopRealtimeList($dimension, $metric = 'activeUsers', $limit = 10, $minutes = 29, $cacheInMinutes = null) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions([$dimension])
            ->setMetrics([$metric])
            ->runReportRealtime($cacheInMinutes)
            ->toArray();

        $result = [];
        foreach ($data as $row) {
            $label = (string) carr::get($row, 'dimensions.' . $dimension . '.value');
            if (strlen($label) == 0) {
                $label = '(tidak disebutkan)';
            }
            $result[] = ['label' => $label, 'value' => (int) carr::get($row, 'metrics.' . $metric . '.value')];
        }

        usort($result, function ($a, $b) {
            return carr::get($b, 'value') - carr::get($a, 'value');
        });

        return array_slice($result, 0, (int) $limit);
    }

    public function getUserIdActiveRealtimeList($customUserPropertyId = 'app_user_id', $minutes = 5, $cacheInMinutes = null) {
        $analytic = $this->analytic;
        $data = $analytic->createReport()
            ->setMinuteRange($minutes, 0)
            ->setDimensions(['customUser:' . $customUserPropertyId])
            ->setMetrics([$analytic->metadata()->realtimeSchema()->metric()->activeUsers()])
            ->runReportRealtime($cacheInMinutes)
            ->toArray();

        $result = [];
        foreach ($data as $row) {
            $userId = carr::get($row, 'dimensions.customUser:' . $customUserPropertyId . '.value');
            $value = carr::get($row, 'metrics.activeUsers.value');
            if ($value > 0) {
                $result[$userId] = $value;
            }
        }

        return $result;
    }
}

<?php

use Google\Analytics\Data\V1beta\BetaAnalyticsDataClient;

class CAnalytics_Google_ClientGA4 {
    /**
     * @var BetaAnalyticsDataClient
     */
    protected $service;

    /**
     * @var \CCache_RepositoryInterface
     */
    protected $cache;

    /**
     * @var int
     */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct(BetaAnalyticsDataClient $service, CCache_RepositoryInterface $cache) {
        $this->service = $service;

        $this->cache = $cache;
    }

    /**
     * Set the cache time.
     *
     * @param int $cacheLifeTimeInMinutes
     *
     * @return self
     */
    public function setCacheLifeTimeInMinutes($cacheLifeTimeInMinutes) {
        $this->cacheLifeTimeInMinutes = $cacheLifeTimeInMinutes * 60;

        return $this;
    }

    /**
     * Query the Google Analytics Service with given parameters.
     *
     * @param array    $reportData
     * @param mixed    $realtime
     * @param null|int $cacheInMinutes
     *
     * @return null|array|\Google\Analytics\Data\V1beta\RunReportResponse
     */
    public function runReport($reportData, $realtime = false, $cacheInMinutes = null) {
        $cacheName = $this->determineCacheName(func_get_args());

        if ($cacheInMinutes === null) {
            $cacheInMinutes = $this->cacheLifeTimeInMinutes;
        }
        if ($cacheInMinutes === 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember(
            $cacheName,
            $cacheInMinutes,
            function () use ($reportData, $realtime) {
                return $realtime ? $this->service->runRealtimeReport($reportData) : $this->service->runReport($reportData);
            }
        );
    }

    /**
     * @return BetaAnalyticsDataClient
     */
    public function getAnalyticsService() {
        return $this->service;
    }

    /**
     * Determine the cache name for the set of query properties given.
     *
     * @param mixed $properties
     */
    protected function determineCacheName($properties) {
        return 'capp.analytics.google.ga4.' . md5(serialize($properties));
    }
}

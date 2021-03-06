<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since May 24, 2019, 1:25:25 AM
 */
class CAnalytics_Google_Client {
    /**
     * @var \Google_Service_Analytics
     */
    protected $service;

    /**
     * @var CCache_Repository
     */
    protected $cache;

    /**
     * @var int
     */
    protected $cacheLifeTimeInMinutes = 0;

    public function __construct(Google_Service_Analytics $service, CCache_Repository $cache) {
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
     * @param string    $viewId
     * @param \DateTime $startDate
     * @param \DateTime $endDate
     * @param string    $metrics
     * @param array     $others
     *
     * @return null|array
     */
    public function performQuery($viewId, DateTime $startDate, DateTime $endDate, $metrics, array $others = []) {
        $cacheName = $this->determineCacheName(func_get_args());
        if ($this->cacheLifeTimeInMinutes == 0) {
            $this->cache->forget($cacheName);
        }

        return $this->cache->remember($cacheName, $this->cacheLifeTimeInMinutes, function () use ($viewId, $startDate, $endDate, $metrics, $others) {
            $result = $this->service->data_ga->get(
                "ga:{$viewId}",
                $startDate->format('Y-m-d'),
                $endDate->format('Y-m-d'),
                $metrics,
                $others
            );
            while ($nextLink = $result->getNextLink()) {
                if (isset($others['max-results']) && count($result->rows) >= $others['max-results']) {
                    break;
                }
                $options = [];
                parse_str(substr($nextLink, strpos($nextLink, '?') + 1), $options);
                $response = $this->service->data_ga->call('get', [$options], 'Google_Service_Analytics_GaData');
                if ($response->rows) {
                    $result->rows = array_merge($result->rows, $response->rows);
                }
                $result->nextLink = $response->nextLink;
            }

            return $result;
        });
    }

    /**
     * @return Google_Service_Analytics
     */
    public function getAnalyticsService() {
        return $this->service;
    }

    /**
     * Determine the cache name for the set of query properties given.
     *
     * @param mixed $key
     */
    protected function determineCacheName(array $properties) {
        return 'capp.analytics.google.' . md5(serialize($properties));
    }
}

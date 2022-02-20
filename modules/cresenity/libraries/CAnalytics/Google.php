<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @see CAnalytics
 * @since Jun 23, 2019, 12:42:50 PM
 */
class CAnalytics_Google {
    use CTrait_Macroable;

    /**
     * @var CAnalytics_Google_Client
     */
    protected $client;

    /**
     * @var string
     */
    protected $viewId;

    /**
     * @param CAnalytics_Google_Client $client
     * @param string                   $viewId
     */
    public function __construct(CAnalytics_Google_Client $client, $viewId) {
        $this->client = $client;
        $this->viewId = $viewId;
    }

    /**
     * @param string $viewId
     *
     * @return $this
     */
    public function setViewId($viewId) {
        $this->viewId = $viewId;

        return $this;
    }

    public function getViewId() {
        return $this->viewId;
    }

    public function fetchVisitorsAndPageViews(CPeriod $period) {
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date,ga:pageTitle']
        );

        return c::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $dateRow) {
            return [
                'date' => CCarbon::createFromFormat('Ymd', $dateRow[0]),
                'pageTitle' => $dateRow[1],
                'visitors' => (int) $dateRow[2],
                'pageViews' => (int) $dateRow[3],
            ];
        });
    }

    public function fetchTotalVisitorsAndPageViews(CPeriod $period) {
        $response = $this->performQuery(
            $period,
            'ga:users,ga:pageviews',
            ['dimensions' => 'ga:date']
        );

        return c::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $dateRow) {
            return [
                'date' => CCarbon::createFromFormat('Ymd', $dateRow[0]),
                'visitors' => (int) $dateRow[1],
                'pageViews' => (int) $dateRow[2],
            ];
        });
    }

    public function fetchMostVisitedPages(CPeriod $period, $maxResults = 20) {
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:pagePath,ga:pageTitle',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults,
            ]
        );

        return c::collect(isset($response['rows']) ? $response['rows'] : [])
            ->map(function (array $pageRow) {
                return [
                    'url' => $pageRow[0],
                    'pageTitle' => $pageRow[1],
                    'pageViews' => (int) $pageRow[2],
                ];
            });
    }

    public function fetchTopReferrers(CPeriod $period, $maxResults = 20) {
        $response = $this->performQuery(
            $period,
            'ga:pageviews',
            [
                'dimensions' => 'ga:fullReferrer',
                'sort' => '-ga:pageviews',
                'max-results' => $maxResults,
            ]
        );

        return c::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $pageRow) {
            return [
                'url' => $pageRow[0],
                'pageViews' => (int) $pageRow[1],
            ];
        });
    }

    public function fetchUserTypes(CPeriod $period) {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:userType',
            ]
        );

        return c::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $userRow) {
            return [
                'type' => $userRow[0],
                'sessions' => (int) $userRow[1],
            ];
        });
    }

    public function fetchTopBrowsers(CPeriod $period, $maxResults = 10) {
        $response = $this->performQuery(
            $period,
            'ga:sessions',
            [
                'dimensions' => 'ga:browser',
                'sort' => '-ga:sessions',
                'max-results' => $maxResults,
            ]
        );
        $topBrowsers = c::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $browserRow) {
            return [
                'browser' => $browserRow[0],
                'sessions' => (int) $browserRow[1],
            ];
        });
        if ($topBrowsers->count() <= $maxResults) {
            return $topBrowsers;
        }

        return $this->summarizeTopBrowsers($topBrowsers, $maxResults);
    }

    protected function summarizeTopBrowsers(CCollection $topBrowsers, $maxResults) {
        return $topBrowsers
            ->take($maxResults - 1)
            ->push([
                'browser' => 'Others',
                'sessions' => $topBrowsers->splice($maxResults - 1)->sum('sessions'),
            ]);
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param Period $period
     * @param string $metrics
     * @param array  $others
     *
     * @return null|array
     */
    public function performQuery(CPeriod $period, $metrics, array $others = []) {
        return $this->client->performQuery(
            $this->viewId,
            $period->startDate,
            $period->endDate,
            $metrics,
            $others
        );
    }

    /**
     * Get the underlying Google_Service_Analytics object. You can use this
     * to basically call anything on the Google Analytics API.
     */
    public function getAnalyticsService() {
        return $this->client->getAnalyticsService();
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 23, 2019, 12:42:50 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CAnalytics_Google {

    use CTrait_Macroable;

    /** @var CAnalytics_Google_Client */
    protected $client;

    /** @var string */
    protected $viewId;

    /**
     * @param array $viewId
     */
    public function __construct($options) {
        $googleServiceAnalytics = new Google_Service_Analytics(CAnalytics_Google_ClientFactory::createAuthenticatedGoogleClient($options));
        $cache = new CAnalytics_Google_Cache(carr::get($options, 'cache'));
        $client = new CAnalytics_Google_Client($googleServiceAnalytics, $cache);
        $client->setCacheLifeTimeInMinutes(carr::get($options, 'cacheLifetime', 60));
        $this->client = $client;
        $this->viewId = carr::get($options, 'viewId');
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

    public function fetchVisitorsAndPageViews(CPeriod $period) {
        $response = $this->performQuery(
                $period, 'ga:users,ga:pageviews', ['dimensions' => 'ga:date,ga:pageTitle']
        );
        return CF::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $dateRow) {
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
                $period, 'ga:users,ga:pageviews', ['dimensions' => 'ga:date']
        );
        return CF::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $dateRow) {
                    return [
                        'date' => CCarbon::createFromFormat('Ymd', $dateRow[0]),
                        'visitors' => (int) $dateRow[1],
                        'pageViews' => (int) $dateRow[2],
                    ];
                });
    }

    public function fetchMostVisitedPages(CPeriod $period, $maxResults = 20) {
        $response = $this->performQuery(
                $period, 'ga:pageviews', [
            'dimensions' => 'ga:pagePath,ga:pageTitle',
            'sort' => '-ga:pageviews',
            'max-results' => $maxResults,
                ]
        );
        return CF::collect(isset($response['rows']) ? $response['rows'] : [])
                        ->map(function (array $pageRow) {
                            return [
                                'url' => $pageRow[0],
                                'pageTitle' => $pageRow[1],
                                'pageViews' => (int) $pageRow[2],
                            ];
                        });
    }

    public function fetchTopReferrers(CPeriod $period, $maxResults = 20) {
        $response = $this->performQuery($period, 'ga:pageviews', [
            'dimensions' => 'ga:fullReferrer',
            'sort' => '-ga:pageviews',
            'max-results' => $maxResults,
                ]
        );
        return CF::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $pageRow) {
                    return [
                        'url' => $pageRow[0],
                        'pageViews' => (int) $pageRow[1],
                    ];
                });
    }

    public function fetchUserTypes(CPeriod $period) {
        $response = $this->performQuery(
                $period, 'ga:sessions', [
            'dimensions' => 'ga:userType',
                ]
        );
        return CF::collect(cobj::get($response, 'rows') ? $response->rows : [])->map(function (array $userRow) {
                    return [
                        'type' => $userRow[0],
                        'sessions' => (int) $userRow[1],
                    ];
                });
    }

    public function fetchTopBrowsers(CPeriod $period, $maxResults = 10) {
        $response = $this->performQuery(
                $period, 'ga:sessions', [
            'dimensions' => 'ga:browser',
            'sort' => '-ga:sessions',
            'max-results' => $maxResults,
                ]
        );
        $topBrowsers = CF::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $browserRow) {
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

    public function fetchActiveUsers() {
        $response = $this->performRealtime(
                'rt:activeVisitors', [
            'dimensions' => 'rt:userType',
            'sort' => '-rt:userType',
                ]
        );

        return $response->totalsForAllResults['rt:activeVisitors'];
    }

    public function fetchActiveUsersByBrowser($maxResults = 20) {
        $response = $this->performRealtime(
                'rt:activeVisitors', [
            'dimensions' => 'rt:browser',
            'sort' => 'rt:browser',
            'max-results' => $maxResults,
                ]
        );

        $topBrowsers = CF::collect(isset($response['rows']) ? $response['rows'] : [])->map(function (array $browserRow) {
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

    /**
     * Get the top keywords.
     *
     * @param CPeriod $period
     * @param int $maxResults
     *
     * @return CCollection
     */
    public function fetchTopKeywords(CPeriod $period, $maxResults = 30) {
        $answer = $this->performQuery($period, 'ga:sessions', array(
            'dimensions' => 'ga:keyword',
            'sort' => '-ga:sessions',
            'max-results' => $maxResults,
            'filters' => 'ga:keyword!=(not set);ga:keyword!=(not provided)'
                )
        );
        if (is_null($answer->rows)) {
            return array();
        }
        $keywordData = array();
        foreach ($answer->rows as $pageRow) {
            $keywordData[] = array(
                'keyword' => $pageRow[0],
                'sessions' => $pageRow[1]
            );
        }
        return $keywordData;
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param Period $period
     * @param string $metrics
     * @param array  $others
     *
     * @return array|null
     */
    public function performQuery(CPeriod $period, $metrics, array $others = []) {
        return $this->client->performQuery(
                        $this->viewId, $period->startDate, $period->endDate, $metrics, $others
        );
    }

    /**
     * Call the query method on the authenticated client.
     *
     * @param string $metrics
     * @param array  $others
     *
     * @return array|null
     */
    public function performRealtime($metrics, array $others = []) {
        return $this->client->performRealtime(
                        $this->viewId, $metrics, $others
        );
    }

    /*
     * Get the underlying Google_Service_Analytics object. You can use this
     * to basically call anything on the Google Analytics API.
     */

    public function getAnalyticsService() {
        return $this->client->getAnalyticsService();
    }

}

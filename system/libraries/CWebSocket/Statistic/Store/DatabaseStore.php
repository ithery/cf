<?php

use Carbon\Carbon;

class CWebSocket_Statistic_Store_DatabaseStore implements CWebSocket_Contract_StatisticStoreInterface {
    /**
     * The model that will interact with the database.
     *
     * @var string
     */
    public static $model = \CWebSocket_Model_WebSocket_Statistic::class;

    /**
     * Store a new record in the database and return
     * the created instance.
     *
     * @param array $data
     *
     * @return mixed
     */
    public static function store(array $data) {
        $model = static::$model;

        return $model::create($data);
    }

    /**
     * Delete records older than the given moment,
     * for a specific app id (if given), returning
     * the amount of deleted records.
     *
     * @param \Carbon\Carbon  $moment
     * @param null|string|int $appId
     *
     * @return int
     */
    public static function delete(Carbon $moment, $appId = null) {
        return static::$model::where('created', '<', $moment->toDateTimeString())
            ->when(!is_null($appId), function ($query) use ($appId) {
                return $query->whereAppId($appId);
            })
            ->delete();
    }

    /**
     * Get the query result as eloquent collection.
     *
     * @param callable $processQuery
     *
     * @return \CCollection
     */
    public function getRawRecords(callable $processQuery = null) {
        return static::$model::query()
            ->when(!is_null($processQuery), function ($query) use ($processQuery) {
                return call_user_func($processQuery, $query);
            }, function ($query) {
                return $query->latest()->limit(120);
            })->get();
    }

    /**
     * Get the results for a specific query.
     *
     * @param callable $processQuery
     * @param callable $processCollection
     *
     * @return array
     */
    public function getRecords(callable $processQuery = null, callable $processCollection = null) {
        return $this->getRawRecords($processQuery)
            ->when(!is_null($processCollection), function ($collection) use ($processCollection) {
                return call_user_func($processCollection, $collection);
            })
            ->map(function (CModel $statistic) {
                return $this->statisticToArray($statistic);
            })
            ->toArray();
    }

    /**
     * Get the results for a specific query into a
     * format that is easily to read for graphs.
     *
     * @param callable $processQuery
     * @param callable $processCollection
     *
     * @return array
     */
    public function getForGraph(callable $processQuery = null, callable $processCollection = null) {
        $statistics = c::collect(
            $this->getRecords($processQuery, $processCollection)
        );

        return $this->statisticsToGraph($statistics);
    }

    /**
     * Turn the statistic model to an array.
     *
     * @param \CModel $statistic
     *
     * @return array
     */
    protected function statisticToArray(CModel $statistic) {
        return [
            'timestamp' => (string) $statistic->created,
            'peak_connection_count' => $statistic->peak_connection_count,
            'websocket_message_count' => $statistic->websocket_message_count,
            'api_message_count' => $statistic->api_message_count,
        ];
    }

    /**
     * Turn the statistics collection to an array used for graph.
     *
     * @param \CCollection $statistics
     *
     * @return array
     */
    protected function statisticsToGraph(CCollection $statistics): array {
        return [
            'peak_connection' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('peak_connection_count')->toArray(),
            ],
            'websocket_message_count' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('websocket_message_count')->toArray(),
            ],
            'api_message_count' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('api_message_count')->toArray(),
            ],
        ];
    }
}

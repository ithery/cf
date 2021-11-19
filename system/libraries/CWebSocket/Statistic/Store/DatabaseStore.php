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
        return static::$model::create($data);
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
    public static function delete(Carbon $moment, $appId = null): int {
        return static::$model::where('created_at', '<', $moment->toDateTimeString())
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
    public function getRecords(callable $processQuery = null, callable $processCollection = null): array {
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
    public function getForGraph(callable $processQuery = null, callable $processCollection = null): array {
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
    protected function statisticToArray(CModel $statistic): array {
        return [
            'timestamp' => (string) $statistic->created_at,
            'peak_connections_count' => $statistic->peak_connections_count,
            'websocket_messages_count' => $statistic->websocket_messages_count,
            'api_messages_count' => $statistic->api_messages_count,
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
            'peak_connections' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('peak_connections_count')->toArray(),
            ],
            'websocket_messages_count' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('websocket_messages_count')->toArray(),
            ],
            'api_messages_count' => [
                'x' => $statistics->pluck('timestamp')->toArray(),
                'y' => $statistics->pluck('api_messages_count')->toArray(),
            ],
        ];
    }
}

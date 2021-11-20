<?php
use React\Promise\PromiseInterface;

interface CWebSocket_Contract_StatisticCollectorInterface {
    /**
     * Handle the incoming websocket message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function webSocketMessage($appId);

    /**
     * Handle the incoming API message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function apiMessage($appId);

    /**
     * Handle the new conection.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function connection($appId);

    /**
     * Handle disconnections.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function disconnection($appId);

    /**
     * Save all the stored statistics.
     *
     * @return void
     */
    public function save();

    /**
     * Flush the stored statistics.
     *
     * @return void
     */
    public function flush();

    /**
     * Get the saved statistics.
     *
     * @return PromiseInterface[array]
     */
    public function getStatistics();

    /**
     * Get the saved statistics for an app.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[\BeyondCode\LaravelWebSockets\Statistics\Statistic|null]
     */
    public function getAppStatistics($appId);

    /**
     * Remove all app traces from the database if no connections have been set
     * in the meanwhile since last save.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function resetAppTraces($appId);
}

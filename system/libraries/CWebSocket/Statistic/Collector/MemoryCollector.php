<?php
use React\Promise\PromiseInterface;

class CWebSocket_Statistic_Collector_MemoryCollector implements CWebSocket_Contract_StatisticCollectorInterface {
    /**
     * The list of stored statistics.
     *
     * @var array
     */
    protected $statistics = [];

    /**
     * The Channel manager.
     *
     * @var \CWebSocket_Contract_ChannelManagerInterface
     */
    protected $channelManager;

    /**
     * Initialize the logger.
     *
     * @return void
     */
    public function __construct() {
        $this->channelManager = CWebSocket::channelManager();
    }

    /**
     * Handle the incoming websocket message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function webSocketMessage($appId) {
        $this->findOrMake($appId)
            ->webSocketMessage();
    }

    /**
     * Handle the incoming API message.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function apiMessage($appId) {
        $this->findOrMake($appId)
            ->apiMessage();
    }

    /**
     * Handle the new conection.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function connection($appId) {
        $this->findOrMake($appId)
            ->connection();
    }

    /**
     * Handle disconnections.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function disconnection($appId) {
        $this->findOrMake($appId)
            ->disconnection();
    }

    /**
     * Save all the stored statistics.
     *
     * @return void
     */
    public function save() {
        $this->getStatistics()->then(function ($statistics) {
            foreach ($statistics as $appId => $statistic) {
                if (!$statistic->isEnabled()) {
                    continue;
                }

                if ($statistic->shouldHaveTracesRemoved()) {
                    $this->resetAppTraces($appId);

                    continue;
                }

                $this->createRecord($statistic, $appId);

                $this->channelManager
                    ->getGlobalConnectionsCount($appId)
                    ->then(function ($connections) use ($statistic) {
                        $statistic->reset(
                            is_null($connections) ? 0 : $connections
                        );
                    });
            }
        });
    }

    /**
     * Flush the stored statistics.
     *
     * @return void
     */
    public function flush() {
        $this->statistics = [];
    }

    /**
     * Get the saved statistics.
     *
     * @return PromiseInterface[array]
     */
    public function getStatistics() {
        return CWebSocket_Helper::createFulfilledPromise($this->statistics);
    }

    /**
     * Get the saved statistics for an app.
     *
     * @param string|int $appId
     *
     * @return PromiseInterface[\BeyondCode\LaravelWebSockets\Statistics\Statistic|null]
     */
    public function getAppStatistics($appId) {
        return CWebSocket_Helper::createFulfilledPromise(
            isset($this->statistics[$appId]) ? $this->statistics[$appId] : null
        );
    }

    /**
     * Remove all app traces from the database if no connections have been set
     * in the meanwhile since last save.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function resetAppTraces($appId) {
        unset($this->statistics[$appId]);
    }

    /**
     * Find or create a defined statistic for an app.
     *
     * @param string|int $appId
     *
     * @return \CWebSocket_Statistic
     */
    protected function findOrMake($appId) {
        if (!isset($this->statistics[$appId])) {
            $this->statistics[$appId] = CWebSocket_Statistic::new($appId);
        }

        return $this->statistics[$appId];
    }

    /**
     * Create a new record using the Statistic Store.
     *
     * @param \CWebSocket_Statistic $statistic
     * @param mixed                 $appId
     *
     * @return void
     */
    public function createRecord(CWebSocket_Statistic $statistic, $appId) {
        CWebSocket::statisticStore()->store($statistic->toArray());
    }
}

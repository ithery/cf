<?php

class CWebSocket_Statistic {
    /**
     * The app id.
     *
     * @var mixed
     */
    protected $appId;

    /**
     * The current connections count ticker.
     *
     * @var int
     */
    protected $currentConnectionsCount = 0;

    /**
     * The peak connections count ticker.
     *
     * @var int
     */
    protected $peakConnectionsCount = 0;

    /**
     * The websockets connections count ticker.
     *
     * @var int
     */
    protected $webSocketMessagesCount = 0;

    /**
     * The api messages connections count ticker.
     *
     * @var int
     */
    protected $apiMessagesCount = 0;

    /**
     * Create a new statistic.
     *
     * @param string|int $appId
     *
     * @return void
     */
    public function __construct($appId) {
        $this->appId = $appId;
    }

    /**
     * Create a new statistic instance.
     *
     * @param string|int $appId
     *
     * @return \CWebSocket_Statistic
     */
    public static function createNew($appId) {
        return new static($appId);
    }

    /**
     * Set the current connections count.
     *
     * @param int $currentConnectionsCount
     *
     * @return $this
     */
    public function setCurrentConnectionsCount($currentConnectionsCount) {
        $this->currentConnectionsCount = $currentConnectionsCount;

        return $this;
    }

    /**
     * Set the peak connections count.
     *
     * @param int $peakConnectionsCount
     *
     * @return $this
     */
    public function setPeakConnectionsCount($peakConnectionsCount) {
        $this->peakConnectionsCount = $peakConnectionsCount;

        return $this;
    }

    /**
     * Set the peak connections count.
     *
     * @param int $webSocketMessagesCount
     *
     * @return $this
     */
    public function setWebSocketMessagesCount($webSocketMessagesCount) {
        $this->webSocketMessagesCount = $webSocketMessagesCount;

        return $this;
    }

    /**
     * Set the peak connections count.
     *
     * @param int $apiMessagesCount
     *
     * @return $this
     */
    public function setApiMessagesCount($apiMessagesCount) {
        $this->apiMessagesCount = $apiMessagesCount;

        return $this;
    }

    /**
     * Check if the app has statistics enabled.
     *
     * @return bool
     */
    public function isEnabled() {
        return CWebSocket_App::findById($this->appId)->statisticsEnabled;
    }

    /**
     * Handle a new connection increment.
     *
     * @return void
     */
    public function connection() {
        $this->currentConnectionsCount++;

        $this->peakConnectionsCount = max($this->currentConnectionsCount, $this->peakConnectionsCount);
    }

    /**
     * Handle a disconnection decrement.
     *
     * @return void
     */
    public function disconnection() {
        $this->currentConnectionsCount--;

        $this->peakConnectionsCount = max($this->currentConnectionsCount, $this->peakConnectionsCount);
    }

    /**
     * Handle a new websocket message.
     *
     * @return void
     */
    public function webSocketMessage() {
        $this->webSocketMessagesCount++;
    }

    /**
     * Handle a new api message.
     *
     * @return void
     */
    public function apiMessage() {
        $this->apiMessagesCount++;
    }

    /**
     * Reset all the connections to a specific count.
     *
     * @param int $currentConnectionsCount
     *
     * @return void
     */
    public function reset($currentConnectionsCount) {
        $this->currentConnectionsCount = $currentConnectionsCount;
        $this->peakConnectionsCount = max(0, $currentConnectionsCount);
        $this->webSocketMessagesCount = 0;
        $this->apiMessagesCount = 0;
    }

    /**
     * Check if the current statistic entry is empty. This means
     * that the statistic entry can be easily deleted if no activity
     * occured for a while.
     *
     * @return bool
     */
    public function shouldHaveTracesRemoved() {
        return $this->currentConnectionsCount === 0 && $this->peakConnectionsCount === 0;
    }

    /**
     * Transform the statistic to array.
     *
     * @return array
     */
    public function toArray() {
        return [
            'app_id' => $this->appId,
            'peak_connection_count' => $this->peakConnectionsCount,
            'websocket_message_count' => $this->webSocketMessagesCount,
            'api_message_count' => $this->apiMessagesCount,
        ];
    }
}

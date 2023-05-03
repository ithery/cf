<?php
interface CBroadcast_SSE_Contract_BroadcastEventHistoryInterface {
    /**
     * @param string $id
     * @param string $channelPrefix
     *
     * @return CCollection
     */
    public function getEventsFrom($id, $channelPrefix);

    /**
     * @param string $channel
     * @param [type] $event
     *
     * @return void
     */
    public function pushEvent($channel, $event);

    /**
     * @return int
     */
    public function lastEventTimestamp();
}

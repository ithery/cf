<?php

class CBroadcast_SSE {
    /**
     * @return CBroadcast_SSE_ServerSentEventStream
     */
    public static function createServerSentEventStream() {
        $eventSubscriber = new CBroadcast_SSE_Subscriber_RedisSubscriber();
        $store = new CBroadcast_SSE_Storage_PresenceChannelUsersRedisRepository();
        $eventHistory = new CBroadcast_SSE_Storage_BroadcastEventHistoryCached();
        $sseStream = new CBroadcast_SSE_ServerSentEventStream($eventSubscriber, $store, $eventHistory);

        return $sseStream;
    }
}

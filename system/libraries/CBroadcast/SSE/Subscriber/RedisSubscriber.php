<?php

class CBroadcast_SSE_Subscriber_RedisSubscriber implements CBroadcast_SSE_Contract_ServerSentEventSubscriberInterface {
    public function start(Closure $onMessage, CHTTP_Request $request) {
        $redisConnectionName = CF::config('broadcast.connections.sse.connection');

        /** @var \CRedis_Connection_PhpRedisConnection|\CRedis_Connection_PredisConnection $connection */
        $connection = CRedis::instance()->connection("{$redisConnectionName}");

        register_shutdown_function(function () use ($request, $connection) {
            if (connection_aborted()) {
                c::event(new CBroadcast_SSE_Event_SseConnectionClosedEvent($request->user(), $request->header('X-Socket-Id')));
            }

            $connection->disconnect();
        });

        $connection->psubscribe('*', $onMessage);
    }
}

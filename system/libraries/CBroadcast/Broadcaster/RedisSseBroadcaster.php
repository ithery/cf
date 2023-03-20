<?php

class CBroadcast_Broadcaster_RedisSseBroadcaster extends CBroadcast_Broadcaster_RedisBroadcaster {
    private $history;

    public function __construct(CBroadcast_SSE_Contract_BroadcastEventHistoryInterface $history, CRedis_FactoryInterface $redis, $connection = null, $prefix = '') {
        $this->history = $history;
        parent::__construct($redis, $connection, $prefix);
    }

    public function broadcast(array $channels, $event, array $payload = []) {
        $payload['broadcast_event_id'] = (string) cstr::uuid();
        $event = 'App\\Events\\' . $event;
        foreach ($this->formatChannels($channels) as $channel) {
            $this->history->pushEvent($channel, [
                'event' => $event,
                'data' => $payload,
                'socket' => carr::get($payload, 'socket'),
            ]);
        }
        parent::broadcast($channels, $event, $payload);
    }
}

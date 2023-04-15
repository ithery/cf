<?php

class CBroadcast_SSE_Storage_BroadcastEventHistoryCached implements CBroadcast_SSE_Contract_BroadcastEventHistoryInterface {
    protected $lifetime;

    /**
     * @var CCache_RepositoryInterface
     */
    protected $cache;

    protected $config;

    public function __construct($cache = null, $lifetime = 60) {
        if ($cache === null) {
            $cache = c::cache()->store();
        }
        $this->cache = $cache;
        if ($lifetime == null) {
            $lifetime = CF::config('broadcast.sse.resume_lifetime', 60);
        }
        $this->lifetime = $lifetime;
    }

    /**
     * @param string $id
     * @param string $channelPrefix
     *
     * @return CCollection
     */
    public function getEventsFrom($id, $channelPrefix) {
        $events = $this->getCached();

        $key = $events->search(function ($item) use ($id, $channelPrefix) {
            $channel = cstr::after($item['channel'], $channelPrefix);

            return $id === ($channel . '.' . $item['event']['data']['broadcast_event_id']);
        });

        return $events->slice($key === false ? 0 : $key + 1);
    }

    /**
     * @return int
     */
    public function lastEventTimestamp(): int {
        $lastEvent = $this->getCached()->last();

        return $lastEvent ? $lastEvent['timestamp'] : 0;
    }

    /**
     * @param string $channel
     * @param mixed  $event
     *
     * @return void
     */
    public function pushEvent($channel, $event) {
        $events = $this->getCached();

        $events->push([
            'channel' => $channel,
            'event' => $event,
            'timestamp' => time(),
        ]);

        $events = $events->filter(function ($event) {
            return time() - $event['timestamp'] < $this->lifetime;
        })->values();

        $this->cache->put('broadcasted_events', $events, $this->lifetime);

        return $events->last()['timestamp'];
    }

    protected function getCached() {
        return $this->cache->get('broadcasted_events', c::collect());
    }
}

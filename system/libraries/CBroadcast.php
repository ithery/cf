<?php

class CBroadcast {
    /**
     * Get Manager Instance.
     *
     * @return CBroadcast_Manager
     */
    public static function manager() {
        return CBroadcast_Manager::instance();
    }

    public static function registerChannel($channel, $callback, $options = []) {
        return static::manager()->driver()->channel($channel, $callback, $options);
    }

    public static function broadcast(array $channels, $event, array $payload = []) {
        return static::manager()->driver()->broadcast($channels, $event, $payload);
    }
}

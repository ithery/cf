<?php

use Pusher\Pusher;

trait CWebSocket_Concern_PushesToPusherTrait {
    /**
     * Get the right Pusher broadcaster for the used driver.
     *
     * @param array $app
     *
     * @return \CBroadcast_BroadcasterAbstract
     */
    public function getPusherBroadcaster(array $app) {
        return new CBroadcast_Broadcaster_PusherBroadcaster(
            new Pusher(
                $app['key'],
                $app['secret'],
                $app['id'],
                CF::config('broadcast.connections.pusher.options', [])
            )
        );
    }
}

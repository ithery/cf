<?php

interface CBroadcast_Contract_ShouldBroadcastInterface {
    /**
     * Get the channels the event should broadcast on.
     *
     * @return \CBroadcast_Channel|\CBroadcast_Channel[]|string[]|string
     */
    public function broadcastOn();
}

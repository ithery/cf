<?php

class CBroadcast_SSE_Event_SsePingEvent implements CBroadcast_Contract_ShouldBroadcastInterface {
    use CEvent_Trait_Dispatchable;
    use CBroadcast_Trait_InteractWithSocketTrait;
    use CQueue_Trait_SerializesModels;

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \CBroadcast_Channel|array
     */
    public function broadcastOn() {
        return new CBroadcast_Channel('general');
    }

    /**
     * @return string
     */
    public function broadcastAs() {
        return 'ping';
    }
}

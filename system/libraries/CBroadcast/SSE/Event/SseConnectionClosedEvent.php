<?php

class CBroadcast_SSE_Event_SseConnectionClosedEvent {
    use CEvent_Trait_Dispatchable;
    use CBroadcast_Trait_InteractWithSocketTrait;
    use CQueue_Trait_SerializesModels;

    /**
     * @var null|CAuth_AuthenticatableInterface
     */
    public $user;

    /**
     * @var string
     */
    public $connectionId;

    /**
     * Create a new event instance.
     *
     * @param mixed $user
     * @param mixed $connectionId
     *
     * @return void
     */
    public function __construct($user, $connectionId) {
        $this->user = $user;
        $this->connectionId = $connectionId;
    }
}

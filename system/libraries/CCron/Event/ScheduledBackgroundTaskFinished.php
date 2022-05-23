<?php

class CCron_Event_ScheduledBackgroundTaskFinished {
    /**
     * The scheduled event that ran.
     *
     * @var \CCron_Event
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param \CCron_Event $task
     *
     * @return void
     */
    public function __construct(CCron_Event $task) {
        $this->task = $task;
    }
}

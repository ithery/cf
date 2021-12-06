<?php

class CCron_Event_ScheduledTaskFinished {
    /**
     * The scheduled event that ran.
     *
     * @var \CCron_Event
     */
    public $task;

    /**
     * The runtime of the scheduled event.
     *
     * @var float
     */
    public $runtime;

    /**
     * Create a new event instance.
     *
     * @param \CCron_Event $task
     * @param float                    $runtime
     *
     * @return void
     */
    public function __construct(CCron_Event $task, $runtime) {
        $this->task = $task;
        $this->runtime = $runtime;
    }
}

<?php
class CCron_Event_ScheduledTaskSkipped {
    /**
     * The scheduled event being run.
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

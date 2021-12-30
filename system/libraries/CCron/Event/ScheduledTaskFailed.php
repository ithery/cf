<?php

class CCron_Event_ScheduledTaskFailed {
    /**
     * The scheduled event that failed.
     *
     * @var CCron_Event
     */
    public $task;

    /**
     * The exception that was thrown.
     *
     * @var \Throwable
     */
    public $exception;

    /**
     * Create a new event instance.
     *
     * @param \CCron_Event $task
     * @param \Throwable               $exception
     *
     * @return void
     */
    public function __construct(CCron_Event $task, $exception) {
        $this->task = $task;
        $this->exception = $exception;
    }
}

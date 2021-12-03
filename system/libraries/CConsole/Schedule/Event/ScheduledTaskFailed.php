<?php

class CConsole_Schedule_Event_ScheduledTaskFailed {
    /**
     * The scheduled event that failed.
     *
     * @var CConsole_Schedule_Event
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
     * @param \CConsole_Schedule_Event $task
     * @param \Throwable               $exception
     *
     * @return void
     */
    public function __construct(CConsole_Schedule_Event $task, $exception) {
        $this->task = $task;
        $this->exception = $exception;
    }
}

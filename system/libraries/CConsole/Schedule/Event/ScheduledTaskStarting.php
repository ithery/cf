<?php

class CConsole_Schedule_Event_ScheduledTaskStarting {
    /**
     * The scheduled event being run.
     *
     * @var \CConsole_Schedule_Event
     */
    public $task;

    /**
     * Create a new event instance.
     *
     * @param \CConsole_Schedule_Event $task
     *
     * @return void
     */
    public function __construct(CConsole_Schedule_Event $task) {
        $this->task = $task;
    }
}

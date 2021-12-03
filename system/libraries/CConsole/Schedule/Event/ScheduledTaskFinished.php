<?php

class CConsole_Schedule_Event_ScheduledTaskFinished {
    /**
     * The scheduled event that ran.
     *
     * @var \CConsole_Schedule_Event
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
     * @param \CConsole_Schedule_Event $task
     * @param float                    $runtime
     *
     * @return void
     */
    public function __construct(CConsole_Schedule_Event $task, $runtime) {
        $this->task = $task;
        $this->runtime = $runtime;
    }
}

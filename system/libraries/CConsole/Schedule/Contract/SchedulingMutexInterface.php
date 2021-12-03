<?php

interface CConsole_Schedule_Contract_SchedulingMutexInterface {
    /**
     * Attempt to obtain a scheduling mutex for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function create(CConsole_Schedule_Event $event, DateTimeInterface $time);

    /**
     * Determine if a scheduling mutex exists for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function exists(CConsole_Schedule_Event $event, DateTimeInterface $time);
}

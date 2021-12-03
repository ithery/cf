<?php

interface CConsole_Schedule_Contract_EventMutexInterface {
    /**
     * Attempt to obtain an event mutex for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return bool
     */
    public function create(CConsole_Schedule_Event $event);

    /**
     * Determine if an event mutex exists for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return bool
     */
    public function exists(CConsole_Schedule_Event $event);

    /**
     * Clear the event mutex for the given event.
     *
     * @param \CConsole_Schedule_Event $event
     *
     * @return void
     */
    public function forget(CConsole_Schedule_Event $event);
}

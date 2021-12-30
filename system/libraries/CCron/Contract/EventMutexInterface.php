<?php

interface CCron_Contract_EventMutexInterface {
    /**
     * Attempt to obtain an event mutex for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return bool
     */
    public function create(CCron_Event $event);

    /**
     * Determine if an event mutex exists for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return bool
     */
    public function exists(CCron_Event $event);

    /**
     * Clear the event mutex for the given event.
     *
     * @param \CCron_Event $event
     *
     * @return void
     */
    public function forget(CCron_Event $event);
}

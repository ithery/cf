<?php

interface CCron_Contract_SchedulingMutexInterface {
    /**
     * Attempt to obtain a scheduling mutex for the given event.
     *
     * @param \CCron_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function create(CCron_Event $event, DateTimeInterface $time);

    /**
     * Determine if a scheduling mutex exists for the given event.
     *
     * @param \CCron_Event $event
     * @param \DateTimeInterface       $time
     *
     * @return bool
     */
    public function exists(CCron_Event $event, DateTimeInterface $time);
}

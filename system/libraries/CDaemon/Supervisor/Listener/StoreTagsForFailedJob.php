<?php

class CDaemon_Supervisor_Listener_StoreTagsForFailedJob {
    /**
     * Handle the event.
     *
     * @param \CDaemon_Supervisor_Event_RedisEvent_JobFailed $event
     *
     * @return void
     */
    public function handle(CDaemon_Supervisor_Event_RedisEvent_JobFailed $event) {
        $tags = c::collect($event->payload->tags())->map(function ($tag) {
            return 'failed:' . $tag;
        })->all();

        CDaemon_Supervisor::tagRepository()->addTemporary(
            2880,
            $event->payload->id(),
            $tags
        );
    }
}

<?php

trait CQueue_Trait_TaskQueue {
    use CQueue_Trait_DispatchableTrait;
    use CQueue_Trait_QueueableTrait;
    use CQueue_Trait_InteractsWithQueue;
    use CQueue_Trait_SerializesModels;

    /**
     * Shortcut function to log the current running service
     *
     * @param string $msg
     */
    public static function logDaemon($msg) {
        CDaemon::log($msg);
    }

    public static function isDaemon() {
        return CDaemon::getRunningService() != null;
    }
}

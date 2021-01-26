<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 8, 2019, 6:06:41 AM
 */
abstract class CQueue_AbstractTask implements CQueue_ShouldQueueInterface {
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

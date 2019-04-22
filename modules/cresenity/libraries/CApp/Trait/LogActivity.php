<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 16, 2019, 2:20:52 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
trait CApp_Trait_LogActivity {

    public static function start($message, $listener = null) {

        if ($listener == null) {
            $listener = array(CApp_Log_Activity::class, 'populate');
        }
        $activity = CModel_Activity::instance();
        $activity->setMessage($message);
        $activity->setListener($listener);
        $activity->start();
    }

    public static function stop() {
        $activity = CModel_Activity::instance();
        $activity->stop();
    }

}

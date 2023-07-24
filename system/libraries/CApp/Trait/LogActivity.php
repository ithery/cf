<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Trait_LogActivity {
    public static function start($message, $listener = null) {
        if ($listener == null) {
            $listener = [static::class, 'populate'];
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

    public static function cancel() {
        $activity = CModel_Activity::instance();
        $activity->cancel();
    }

    public static function populate($description, $data) {
        CApp_Log_Activity::populate($description, $data);
    }
}

<?php

defined('SYSPATH') or die('No direct access allowed.');

trait CApp_Trait_LogActivity {
    /**
     * @param string        $message
     * @param callable|null $listener
     *
     * @return void
     */
    public static function start($message, $listener = null) {
        if ($listener == null) {
            $listener = [static::class, 'populate'];
        }
        $activity = CModel_Activity::instance();
        $activity->setMessage($message);
        $activity->setListener($listener);
        $activity->start();
    }

    /**
     * @return void
     */
    public static function stop() {
        $activity = CModel_Activity::instance();
        $activity->stop();
    }

    /**
     * @return void
     */
    public static function cancel() {
        $activity = CModel_Activity::instance();
        $activity->cancel();
    }

    /**
     * @param string $description
     * @param mixed  $data
     *
     * @return void
     */
    public static function populate($description, $data) {
        CApp_Log_Activity::populate($description, $data);
    }
}

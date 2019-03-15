<?php

use CEvent_Dispatcher as Dispatcher;

/**
 * 
 */
trait CModel_Activity_ActivityTrait {

    public static function bootLog($userId, $message, CModel $model, $observer) {
        $observer->start($userId, $message, $model);
        $model->setEventDispatcher(new Dispatcher());
        $model->observe($observer);
    }

}

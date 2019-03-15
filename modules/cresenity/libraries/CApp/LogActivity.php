<?php

use CApp_Model_Observer_LogActivity as Observer;

/**
 * 
 */
class CApp_LogActivity {

    private static $instance;
    private $isStarted;
    private $model;
    private $observer;
    private $message;

    private function __construct() {
        $this->isStarted = false;
        $this->model = null;
        $this->observer = Observer::class;
    }

    /**
     * 
     * @return CApp_LogActivity
     */
    public static function instance() {
        if (!static::$instance) {
            static::$instance = new static;
        }

        return static::$instance;
    }

    public function start($message, $model, $observer = null) {
        if (!$model) {
            $model = new CApp_Model_LogActivity();
        } elseif (!$model instanceof CModel && is_string($model)) {
            $model = new $model();
        }
        if (!$model instanceof CModel) {
            throw new CApp_Exception('instance for start is not a model');
        }
        $userId = CApp_Base::userId();
        $activity = CModel_Activity::instance();
        $activity->setModel($model);
        $activity->setMessage($message);
        $activity->setObserver($observer);
        $activity->setUserId($userId);
        $activity->start();
    }

    public function stop() {
        $activity = CModel_Activity::instance();
        $activity->stop();
    }

}

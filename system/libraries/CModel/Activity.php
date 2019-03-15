<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 6:47:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CModel_Activity {

    use CEvent_Trait_Dispatchable;

    private $isStarted;
    private $userId;
    private $message;
    private $modelLogActivity;
    private static $instance;

    /**
     * 
     * @return CModel_Activity
     */
    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new static();
        }
        return self::$instance;
    }

    private function __construct() {
        
    }

    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function setModel($model) {
        $this->modelLogActivity = $model;
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setListener($callback) {

        $this->listen('OnActivity', $callback);
    }

    public function start() {
        $this->isStarted = true;
    }

    public function stop() {
        $this->isStarted = false;
    }

    public function isStarted() {
        return $this->isStarted;
    }

}

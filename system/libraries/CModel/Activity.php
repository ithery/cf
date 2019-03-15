<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Mar 15, 2019, 6:47:31 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
final class CModel_Activity {

    private $isStarted;
    private $userId;
    private $message;
    private $modelLogActivity;
    private $observer;
    private static $instance;

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

    public function setObserver($observer) {
        $this->observer = $observer;
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

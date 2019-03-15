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
    private $message;
    private static $instance;
    private $data;

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
        $this->data = array();
    }

    public function setMessage($message) {
        $this->message = $message;
    }

    public function setListener($callback) {

        $this->listen('OnActivity', $callback);
    }

    public function start() {
        $this->isStarted = true;
        $this->data = array();
    }

    public function stop() {
        $this->dispatch('OnActivity', array($this->message, $this->data));
        $this->isStarted = false;
    }

    public function addData($table, $key, $type, $before, $after, $changes) {
        $d['table'] = $table;
        $d['key'] = $key;
        $d['type'] = $type;
        $d['before'] = $before;
        $d['after'] = $after;
        $d['changes'] = $changes;
        $this->data[] = $d;
    }

    public function isStarted() {
        return $this->isStarted;
    }

}

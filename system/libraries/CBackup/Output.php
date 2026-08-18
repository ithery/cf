<?php

class CBackup_Output {
    private static $instance;
    protected $messages;
    protected $errors;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new CBackup_Output();
        }
        return static::$instance;
    }

    private function __construct() {
        $this->clear();
    }

    public function info($info) {
        $this->messages[] = $info;
    }

    public function error($message) {
        $this->messages[] = 'ERROR:' . $message;
        $this->errors[] = $message;
    }

    public function clear() {
        $this->messages = [];
    }

    public function getOutput() {
        return $this->messages;
    }

    public function getAndClearOutput() {
        $messages = $this->messages;
        $this->clear();
        return $messages;
    }
}

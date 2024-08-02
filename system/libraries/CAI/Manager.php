<?php

class CAI_Manager {
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function createOpenAIService($options = []) {
        return new CAI_Service_OpenAIService($options);
    }

    public function createOllamaService() {
    }

    public function service($name = null) {
    }

    public function resolveService($name) {
    }
}

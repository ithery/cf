<?php

class CAI_Manager {
    private static $instance;

    public static function instance() {
        if (self::$instance == null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @param array $options
     *
     * @return CAI_Service_OpenAIServicevoid
     */
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

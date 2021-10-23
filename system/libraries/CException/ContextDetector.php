<?php

class CException_ContextDetector implements CException_Contract_ContextDetectorInterface {
    /**
     * CException_Contract_ContextInterface.
     *
     * @return void
     */
    public function detectCurrentContext() {
        if ($this->runningInConsole()) {
            return new CException_Context_ConsoleContext(isset($_SERVER['argv']) ? $_SERVER['argv'] : []);
        }

        return new CException_Context_RequestContext(CHTTP::request());
    }

    private function runningInConsole(): bool {
        if (isset($_ENV['APP_RUNNING_IN_CONSOLE'])) {
            return $_ENV['APP_RUNNING_IN_CONSOLE'] === 'true';
        }

        if (isset($_ENV['CF_FAKE_WEB_REQUEST'])) {
            return false;
        }

        return in_array(php_sapi_name(), ['cli', 'phpdb']);
    }
}

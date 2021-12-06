<?php

class CEnv_Adapter_NullAdapter implements CEnv_AdapterInterface {
    public function __construct() {
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null) {
        return $default;
    }

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public function enablePutenv() {
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public function disablePutenv() {
    }
}

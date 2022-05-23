<?php

interface CEnv_AdapterInterface {
    /**
     * Gets the value of an environment variable.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get($key, $default = null);

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public function enablePutenv();

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public function disablePutenv();
}

<?php

class CEnv_Adapter_PhpAdapter implements CEnv_AdapterInterface {
    /**
     * Data from Env.
     *
     * @var array
     */
    protected $data;

    public function __construct() {
        $file = c::appRoot() . 'env.php';
        if (!CFile::exists($file)) {
            throw new Exception($file . ' not exists');
        }
        $this->data = include $file;
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
        return carr::get($this->data, $key, $default);
    }

    /**
     * Enable the putenv adapter.
     *
     * @return void
     */
    public function enablePutenv() {
        throw new Exception('Env PHPAdapter not supported for doing this');
    }

    /**
     * Disable the putenv adapter.
     *
     * @return void
     */
    public function disablePutenv() {
        throw new Exception('Env PHPAdapter not supported for doing this');
    }
}

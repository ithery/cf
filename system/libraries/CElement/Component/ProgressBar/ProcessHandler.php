<?php

class CElement_Component_ProgressBar_ProcessHandler {
    /**
     * @var array
     */
    protected $config;

    /**
     * @var callable|string|Opis\Closure\SerializableClosure|CFunction_SerializableClosure
     */
    protected $process;

    /**
     * @var string|null
     */
    protected $updateMethod;

    /**
     * @var callable|string|Opis\Closure\SerializableClosure|CFunction_SerializableClosure|null
     */
    protected $onNotify;

    /**
     * @param callable|string|Opis\Closure\SerializableClosure|CFunction_SerializableClosure $process
     * @param array                                                                          $config
     *
     * @return void
     */
    public function __construct($process, $config) {
        $this->process = $process;
        $this->updateMethod = carr::get($config, 'updateMethod');
        $this->config = $config;
    }

    /**
     * @param callable|string|Opis\Closure\SerializableClosure|CFunction_SerializableClosure $callback
     *
     * @return void
     */
    public function setNotifyListener($callback) {
        $this->onNotify = $callback;
    }

    /**
     * @param mixed $data
     *
     * @return mixed
     */
    public function notify($data) {
        $updateMethod = $this->updateMethod;
        $json = json_encode($data);
        $js = <<<JAVASCRIPT
            parent.{$updateMethod}({$json});
        JAVASCRIPT;
        //echo $js;
        $script = '<script type="text/javascript">' . $js . '</script>' . "\n\n";

        return c::call($this->onNotify, [$script]);
    }

    /**
     * @return mixed
     */
    public function startProcess() {
        return c::call($this->process, [new CElement_Component_ProgressBar_ProgressHandler($this, $this->config)]);
    }
}

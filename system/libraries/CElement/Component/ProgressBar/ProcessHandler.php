<?php

class CElement_Component_ProgressBar_ProcessHandler {
    protected $config;

    protected $process;

    protected $updateMethod;

    protected $onNotify;

    public function __construct($process, $config) {
        $this->process = $process;
        $this->updateMethod = carr::get($config, 'updateMethod');
        $this->config = $config;
    }

    public function setNotifyListener($callback) {
        $this->onNotify = $callback;
    }

    public function notify($data) {
        $updateMethod = $this->updateMethod;
        $json = json_encode($data);
        $js = <<<JAVASCRIPT
            parent.${updateMethod}(${json});
        JAVASCRIPT;
        //echo $js;
        $script = '<script type="text/javascript">' . $js . '</script>' . "\n\n";

        return c::call($this->onNotify, [$script]);
    }

    public function startProcess() {
        return c::call($this->process, [new CElement_Component_ProgressBar_ProgressHandler($this, $this->config)]);
    }
}

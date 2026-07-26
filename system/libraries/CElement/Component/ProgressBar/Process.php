<?php

class CElement_Component_ProgressBar_Process {
    /**
     * @var callable|CFunction_SerializableClosure
     */
    protected $callable;

    /**
     * @param callable|Closure $callback
     *
     * @return void
     */
    public function __construct($callback) {
        $this->callable = c::toSerializableClosure($callback);
    }

    /**
     * @param array $config
     *
     * @return string
     */
    public function createIframeProcess(array $config) {
        $ajaxMethod = CAjax::createMethod();
        $ajaxMethod->setType('ProgressBarProcess');
        $ajaxMethod->setData('callable', serialize($this->callable));
        $ajaxMethod->setData('config', $config);
        $ajaxUrl = $ajaxMethod->makeUrl();
        $iframeId = carr::get($config, 'id') . '-iframe-process';

        $markup = '<iframe src="' . $ajaxUrl . '" id="' . $iframeId . '" class="cres-progress-bar-iframe"></iframe>';

        return $markup;
    }
}

<?php

class CElement_Component_ProgressBar_ProgressHandler {
    protected $value;

    protected $maxValue;

    protected $minValue;

    protected $timeTaken;

    protected $timeRemaining;

    protected $label;

    protected $updateMethod;

    /**
     * @var CElement_Component_ProgressBar_ProcessHandler
     */
    protected $process;

    public function __construct(CElement_Component_ProgressBar_ProcessHandler $process, array $config) {
        $this->value = carr::get($config, 'value');
        $this->updateMethod = carr::get($config, 'updateMethod');
        $this->process = $process;
    }

    public function setValue($value) {
        $this->value = $value;
    }

    public function notify() {
        return $this->process->notify($this->getData());
    }

    protected function getData() {
        $data = [
            'value' => $this->value,
            'maxValue' => $this->maxValue,
            'minValue' => $this->minValue,
            'timeTaken' => $this->timeTaken,
            'timeRemaining' => $this->timeRemaining,
            'label' => $this->label
        ];

        return $data;
    }
}

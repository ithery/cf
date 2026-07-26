<?php

class CElement_Component_ProgressBar_ProgressHandler {
    /**
     * @var int|null
     */
    protected $value;

    /**
     * @var int|null
     */
    protected $maxValue;

    /**
     * @var int|null
     */
    protected $minValue;

    /**
     * @var mixed
     */
    protected $timeTaken;

    /**
     * @var mixed
     */
    protected $timeRemaining;

    /**
     * @var string|null
     */
    protected $label;

    /**
     * @var string|null
     */
    protected $updateMethod;

    /**
     * @var CElement_Component_ProgressBar_ProcessHandler
     */
    protected $process;

    /**
     * @param CElement_Component_ProgressBar_ProcessHandler $process
     * @param array                                         $config
     *
     * @return void
     */
    public function __construct(CElement_Component_ProgressBar_ProcessHandler $process, array $config) {
        $this->value = carr::get($config, 'value');
        $this->maxValue = carr::get($config, 'maxValue');
        $this->minValue = carr::get($config, 'minValue');
        $this->updateMethod = carr::get($config, 'updateMethod');
        $this->process = $process;
    }

    /**
     * @param int $value
     *
     * @return void
     */
    public function setValue($value) {
        $this->value = $value;
    }

    /**
     * @return mixed
     */
    public function notify() {
        return $this->process->notify($this->getData());
    }

    /**
     * @return array
     */
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

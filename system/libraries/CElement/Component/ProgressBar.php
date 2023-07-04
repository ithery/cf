<?php

class CElement_Component_ProgressBar extends CElement_Component {
    protected $minValue;

    protected $maxValue;

    protected $value;

    /**
     * @var CElement_Element_Div
     */
    protected $bar;

    /**
     * @var null|CElement_Component_ProgressBar_Process
     */
    protected $process;

    public function __construct($id = null) {
        parent::__construct($id);
        $this->minValue = 0;
        $this->maxValue = 100;
        $this->value = 0;
        $this->bar = $this->addDiv();
        $this->wrapper = $this->bar;
        $this->process = null;
    }

    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    public function withProcess($process) {
        if (!$process instanceof CElement_Component_ProgressBar_Process) {
            $process = new CElement_Component_ProgressBar_Process($process);
        }
        $this->process = $process;

        return $this;
    }

    public function build() {
        $this->addClass('cres:element:component:ProgressBar');
        $this->addClass('progress');
        $this->addClass('cres-progress');

        $this->bar->addClass('progress-bar');
        $this->bar->addClass('cres-progress-bar');

        $this->bar->setAttr('role', 'progressbar');
        $this->bar->setAttr('aria-valuenow', $this->value);
        $this->bar->setAttr('aria-valuemin', $this->minValue);
        $this->bar->setAttr('aria-valuemax', $this->maxValue);
        $this->setAttr('cres-element', 'component:ProgressBar');
        $config = [
            'minValue' => $this->minValue,
            'maxValue' => $this->maxValue,
            'value' => $this->value,
            'updateMethod' => 'cres_update_progressbar_' . $this->id,
            'id' => $this->id,
        ];
        $this->setAttr('cres-config', c::json($config));
        if ($this->process != null) {
            $this->add($this->process->createIframeProcess($config));
        }
    }
}

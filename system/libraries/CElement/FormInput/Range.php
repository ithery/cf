<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Range extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;
    use CTrait_Element_Property_ApplyJs;

    protected $min;

    protected $max;

    protected $step;

    protected $valueContainerSelector;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = 'range';
        $this->placeholder = '';
        $this->addClass('form-control form-range');
        $this->applyJs = c::theme('range.applyJs');
        $this->min = 0;
        $this->max = 100;
        $this->step = 1;
    }

    public function setMin($min) {
        $this->min = $min;

        return $this;
    }

    public function setMax($max) {
        $this->max = $max;

        return $this;
    }

    public function setStep($step) {
        $this->step = $step;

        return $this;
    }

    public function setValueContainerSelector($selector) {
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        $this->valueContainerSelector = $selector;

        return $this;
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
        $this->setAttr('placeholder', $this->placeholder);
        $this->setAttr('min', $this->min);
        $this->setAttr('max', $this->max);
        $this->setAttr('step', $this->step);
        if ($this->readonly) {
            $this->setAttr('readonly', 'readonly');
        }
        if ($this->applyJs == 'ion-rangeslider') {
            CManager::instance()->registerModule('ion-rangeslider');
        }
    }

    public function js($indent = 0) {
        $js = '';
        if ($this->applyJs == 'ion-rangeslider') {
            $jsonParams = '';
            $jsonParams .= 'min:' . $this->min . ',';
            $jsonParams .= 'max:' . $this->max . ',';
            $jsonParams .= 'step:' . $this->step . ',';
            if ($this->value) {
                $jsonParams .= 'from:' . $this->value . ',';
            }
            $js .= "$('#" . $this->id . "').ionRangeSlider({
                " . $jsonParams . '
            });';
        }
        if ($this->valueContainerSelector != null) {
            if ($this->applyJs == null) {
                $js .= "$('#" . $this->id . "').on('change',function() {
                    $('" . $this->valueContainerSelector . "').html(this.value);
                }).trigger('change');";
            }
        }
        $js .= $this->jsChild($indent);

        return $js;
    }
}

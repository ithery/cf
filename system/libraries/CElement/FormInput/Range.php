<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Range extends CElement_FormInput {
    use CTrait_Element_Property_Placeholder;
    use CTrait_Element_Property_ApplyJs;

    /**
     * @var int
     */
    protected $min;

    /**
     * @var int
     */
    protected $max;

    /**
     * @var int
     */
    protected $step;

    /**
     * CSS selector (or `CRenderable` resolved to `#id`) of the element that
     * mirrors this range's current value on change.
     *
     * @var null|string
     */
    protected $valueContainerSelector;

    /**
     * @param string $id
     */
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

    /**
     * @param int $min
     *
     * @return $this
     */
    public function setMin($min) {
        $this->min = $min;

        return $this;
    }

    /**
     * @param int $max
     *
     * @return $this
     */
    public function setMax($max) {
        $this->max = $max;

        return $this;
    }

    /**
     * @param int $step
     *
     * @return $this
     */
    public function setStep($step) {
        $this->step = $step;

        return $this;
    }

    /**
     * @param string|CRenderable $selector
     *
     * @return $this
     */
    public function setValueContainerSelector($selector) {
        if ($selector instanceof CRenderable) {
            $selector = '#' . $selector->id();
        }
        $this->valueContainerSelector = $selector;

        return $this;
    }

    /**
     * @return void
     */
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

    /**
     * @param int $indent
     *
     * @return string
     */
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

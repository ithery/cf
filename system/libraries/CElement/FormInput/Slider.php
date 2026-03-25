<?php

defined('SYSPATH') or die('No direct access allowed.');

class CElement_FormInput_Slider extends CElement_FormInput {
    protected $minValue;

    protected $maxValue;

    protected $value;

    protected $step;

    protected $orientation;

    protected $tooltip;

    protected $onSlide;

    protected $onSlideStart;

    protected $onSlideStop;

    public function __construct($id = '') {
        parent::__construct($id);

        $this->minValue = 0;
        $this->maxValue = 10;
        $this->value = 0;
        $this->step = 1;
        $this->orientation = 'horizontal';
        $this->tooltip = 'show';
    }

    public static function factory($id = '') {
        return new CElement_FormInput_Slider($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();

        $classes = $this->classes;
        $classes = implode(' ', $classes);
        if (strlen($classes) > 0) {
            $classes = ' ' . $classes;
        }

        $html->append('<input type="text" id="' . $this->id . '" name="' . $this->name . '" '
                . ' class="' . $classes . '" />');

        $html->appendln(parent::html($indent));

        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();

        $js->append("jQuery('#" . $this->id . "').slider({");
        $js->append("   'range': true,");
        $js->append("   'min': " . $this->minValue . ',');
        $js->append("   'max': " . $this->maxValue . ',');
        $js->append("   'value': " . $this->value . ',');
        $js->append("   'step': " . $this->step . ',');
        $js->append("   'orientation': '" . $this->orientation . "',");
        $js->append("   'tooltip': '" . $this->tooltip . "',");
        $js->append('})');

        if (strlen($this->onSlide) > 0) {
            $js->append(".on('slide', function(e) {");
            $js->append($this->onSlide);
            $js->append('})');
        }
        if (strlen($this->onSlideStop) > 0) {
            $js->append(".on('slideStop', function(e) {");
            $js->append($this->onSlideStop);
            $js->append('})');
        }
        $js->append(';');

        $js->appendln(parent::js($indent));

        return $js->text();
    }

    public function setMinValue($minValue) {
        $this->minValue = $minValue;

        return $this;
    }

    public function setMaxValue($maxValue) {
        $this->maxValue = $maxValue;

        return $this;
    }

    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    public function setTooltip($tooltip) {
        $this->tooltip = $tooltip;

        return $this;
    }

    public function setStep($step) {
        $this->step = $step;

        return $this;
    }
}

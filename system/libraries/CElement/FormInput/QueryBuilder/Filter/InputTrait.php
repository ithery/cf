<?php

use CElement_FormInput_QueryBuilder_Constant as Constant;

trait CElement_FormInput_QueryBuilder_Filter_InputTrait {
    protected $input;

    public function setInputText() {
        $this->input = Constant::FILTER_INPUT_TEXT;
        $this->values = null;

        return $this;
    }

    public function setInputTextarea() {
        $this->input = Constant::FILTER_INPUT_TEXTAREA;
        $this->values = null;

        return $this;
    }

    public function setInputSelect($list) {
        $this->input = Constant::FILTER_INPUT_SELECT;
        $this->values = $list;

        return $this;
    }

    public function setInputRadio($list) {
        $this->input = Constant::FILTER_INPUT_RADIO;
        $this->values = $list;

        return $this;
    }

    public function setInputCheckbox($list) {
        $this->input = Constant::FILTER_INPUT_CHECKBOX;
        $this->values = $list;

        return $this;
    }
}

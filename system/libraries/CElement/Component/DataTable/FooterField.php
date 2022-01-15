<?php

class CElement_Component_DataTable_FooterField {
    protected $label;

    protected $labelAlign;

    protected $value;

    protected $align;

    protected $dataType;

    protected $labelColSpan = 0;

    public function __construct($array = []) {
        $this->label = carr::get($array, 'label', '');
        $this->value = carr::get($array, 'value', '');
        $this->align = carr::get($array, 'align', 'left');
        $this->labelAlign = carr::get($array, 'labelAlign', 'right');
        $this->dataType = carr::get($array, 'dataType', 'string');
        $this->labelColSpan = carr::get($array, 'labelColSpan', 0);
    }

    public function getLabel() {
        return $this->label;
    }

    public function getDataType() {
        return $this->dataType;
    }

    public function getLabelColSpan() {
        return $this->labelColSpan;
    }

    public function getValue() {
        return $this->value;
    }

    public function getLabelAlign() {
        return $this->labelAlign;
    }

    public function getAlign() {
        return $this->align;
    }

    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    public function setDataType($dataType) {
        $this->dataType = $dataType;

        return $this;
    }

    public function setLabelColSpan($colSpan) {
        $this->labelColSpan = $colSpan;

        return $this;
    }

    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    public function setLabelAlign($align) {
        $this->labelAlign = $align;

        return $this;
    }

    public function setAlign($align) {
        $this->align = $align;

        return $this;
    }
}

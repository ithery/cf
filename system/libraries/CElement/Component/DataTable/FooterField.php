<?php

class CElement_Component_DataTable_FooterField {
    /**
     * @var string
     */
    protected $label;

    /**
     * @var string
     */
    protected $labelAlign;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var string
     */
    protected $align;

    /**
     * @var string
     */
    protected $dataType;

    /**
     * @var int
     */
    protected $labelColSpan = 0;

    /**
     * @param array $array
     *
     * @return void
     */
    public function __construct($array = []) {
        $this->label = carr::get($array, 'label', '');
        $this->value = carr::get($array, 'value', '');
        $this->align = carr::get($array, 'align', 'left');
        $this->labelAlign = carr::get($array, 'labelAlign', 'right');
        $this->dataType = carr::get($array, 'dataType', 'string');
        $this->labelColSpan = carr::get($array, 'labelColSpan', 0);
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getDataType() {
        return $this->dataType;
    }

    /**
     * @return int
     */
    public function getLabelColSpan() {
        return $this->labelColSpan;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getLabelAlign() {
        return $this->labelAlign;
    }

    /**
     * @return string
     */
    public function getAlign() {
        return $this->align;
    }

    /**
     * @param string $label
     *
     * @return $this
     */
    public function setLabel($label) {
        $this->label = $label;

        return $this;
    }

    /**
     * @param string $dataType
     *
     * @return $this
     */
    public function setDataType($dataType) {
        $this->dataType = $dataType;

        return $this;
    }

    /**
     * @param int $colSpan
     *
     * @return $this
     */
    public function setLabelColSpan($colSpan) {
        $this->labelColSpan = $colSpan;

        return $this;
    }

    /**
     * @param mixed $value
     *
     * @return $this
     */
    public function setValue($value) {
        $this->value = $value;

        return $this;
    }

    /**
     * @param string $align
     *
     * @return $this
     */
    public function setLabelAlign($align) {
        $this->labelAlign = $align;

        return $this;
    }

    /**
     * @param string $align
     *
     * @return $this
     */
    public function setAlign($align) {
        $this->align = $align;

        return $this;
    }
}

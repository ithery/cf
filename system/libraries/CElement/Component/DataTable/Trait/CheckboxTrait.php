<?php

trait CElement_Component_DataTable_Trait_CheckboxTrait {
    public $checkbox;

    public $checkboxColumnWidth;

    /**
     * Checkbox Value.
     *
     * @var array
     */
    public $checkboxValue;

    public $checkboxRenderer = [CElement_Component_DataTable_Renderer::class, 'checkboxCell'];

    /**
     * @param int $width
     *
     * @return $this
     */
    public function setCheckboxColumnWidth($width) {
        $this->checkboxColumnWidth = $width;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setCheckbox($bool) {
        $this->checkbox = $bool;

        return $this;
    }

    /**
     * @param string $val
     *
     * @return $this
     */
    public function setCheckboxValue($val) {
        $this->checkboxValue = carr::wrap($val);

        return $this;
    }

    /**
     * @return array
     */
    public function getCheckboxValue() {
        return  $this->checkboxValue;
    }

    public function enableCheckbox() {
        $this->checkbox = true;

        return $this;
    }

    public function disableCheckbox() {
        $this->checkbox = false;

        return $this;
    }

    public function setCheckboxRenderer($renderer) {
        $this->checkboxRenderer = $renderer;

        return $this;
    }

    public function getCheckboxRenderer() {
        return $this->checkboxRenderer;
    }

    public function callCheckboxRenderer($row) {
        return call_user_func_array($this->getCheckboxRenderer(), [$this, $row]);
    }
}

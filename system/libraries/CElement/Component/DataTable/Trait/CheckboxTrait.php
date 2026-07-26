<?php

trait CElement_Component_DataTable_Trait_CheckboxTrait {
    /**
     * @var null|bool
     */
    public $checkbox;

    /**
     * @var null|int|string
     */
    public $checkboxColumnWidth;

    /**
     * Checkbox Value.
     *
     * @var array
     */
    public $checkboxValue;

    /**
     * @var callable
     */
    public $checkboxRenderer = [CElement_Component_DataTable_Renderer::class, 'checkboxCell'];

    /**
     * @param int|string $width
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
     * @param mixed $val
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

    /**
     * @return $this
     */
    public function enableCheckbox() {
        $this->checkbox = true;

        return $this;
    }

    /**
     * @return $this
     */
    public function disableCheckbox() {
        $this->checkbox = false;

        return $this;
    }

    /**
     * @param callable $renderer
     *
     * @return $this
     */
    public function setCheckboxRenderer($renderer) {
        $this->checkboxRenderer = $renderer;

        return $this;
    }

    /**
     * @return callable
     */
    public function getCheckboxRenderer() {
        return $this->checkboxRenderer;
    }

    /**
     * @param mixed $row
     *
     * @return string
     */
    public function callCheckboxRenderer($row) {
        return call_user_func_array($this->getCheckboxRenderer(), [$this, $row]);
    }
}

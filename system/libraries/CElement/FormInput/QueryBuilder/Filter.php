<?php

use Illuminate\Contracts\Support\Arrayable;
use CElement_FormInput_QueryBuilder_Constant as Constant;

class CElement_FormInput_QueryBuilder_Filter implements Arrayable {
    use CElement_FormInput_QueryBuilder_Filter_OperatorTrait;
    use CElement_FormInput_QueryBuilder_Filter_InputTrait;

    /**
     * @var null|string
     */
    protected $id;

    /**
     * @var string
     */
    protected $label;

    /**
     * One of the `CElement_FormInput_QueryBuilder_Constant::FILTER_TYPE_*` constants.
     *
     * @var string
     */
    protected $type;

    /**
     * The list of selectable values, for select/radio/checkbox inputs.
     *
     * @var null|array
     */
    protected $values;

    /**
     * @var null|array
     */
    protected $validation;

    /**
     * @var null|string
     */
    protected $placeholder;

    /**
     * @var null|bool
     */
    protected $multiple;

    /**
     * @param null|string $id
     *
     * @return void
     */
    public function __construct($id = null) {
        $this->id = $id;
        $this->label = 'Name';

        $this->type = Constant::FILTER_TYPE_STRING;
        $this->values = null;
        $this->input = null;
        $this->validation = null;
        $this->placeholder = null;
        $this->multiple = null;
    }

    /**
     * @param string $id
     *
     * @return $this
     */
    public function setId($id) {
        $this->id = $id;

        return $this;
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
     * @return $this
     */
    public function setTypeString() {
        $this->type = Constant::FILTER_TYPE_STRING;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeInteger() {
        $this->type = Constant::FILTER_TYPE_INTEGER;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDouble() {
        $this->type = Constant::FILTER_TYPE_DOUBLE;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDate() {
        $this->type = Constant::FILTER_TYPE_DATE;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeTime() {
        $this->type = Constant::FILTER_TYPE_TIME;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeDatetime() {
        $this->type = Constant::FILTER_TYPE_DATETIME;

        return $this;
    }

    /**
     * @return $this
     */
    public function setTypeBoolean() {
        $this->type = Constant::FILTER_TYPE_BOOLEAN;

        return $this;
    }

    /**
     * @param string $type one of the `CElement_FormInput_QueryBuilder_Constant::FILTER_TYPE_*` constants
     *
     * @return $this
     */
    public function setType($type) {
        $this->type = $type;

        return $this;
    }

    /**
     * @param bool $bool
     *
     * @return $this
     */
    public function setMultiple($bool = true) {
        $this->multiple = $bool;

        return $this;
    }

    /**
     * @param array $validation
     *
     * @return $this
     */
    public function setValidation(array $validation) {
        $this->validation = $validation;

        return $this;
    }

    /**
     * @param string $placeholder
     *
     * @return $this
     */
    public function setPlaceholder($placeholder) {
        $this->placeholder = $placeholder;

        return $this;
    }

    /**
     * Convert this filter into the array shape expected by the jQuery QueryBuilder `filters` option.
     *
     * @return array
     */
    public function toArray() {
        $result = [];
        $result['id'] = $this->id;
        $result['label'] = $this->label;
        $result['type'] = $this->type;
        if ($this->input) {
            $result['input'] = $this->input;
        }

        if ($this->multiple) {
            $result['multiple'] = $this->multiple;
        }

        if ($this->values) {
            $result['values'] = $this->values;
        }

        // @phpstan-ignore-next-line
        if ($this->operators && is_array($this->operators) && count($this->operators) > 0) {
            $result['operators'] = $this->operators;
        }
        // @phpstan-ignore-next-line
        if ($this->validation && is_array($this->validation) && count($this->validation) > 0) {
            $result['validation'] = $this->validation;
        }

        if ($this->placeholder) {
            $result['placeholder'] = $this->placeholder;
        }

        $result['input_event'] = 'change input';

        return $result;
    }
}

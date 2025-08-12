<?php

use CElement_FormInput_QueryBuilder_Constant as Constant;

trait CElement_FormInput_QueryBuilder_Filter_OperatorTrait {
    protected $operators = [];

    protected $defaultOperator;

    /**
     * @param string $operator [equal,not_equal,in,not_in,less,less_or_equal,greater,greater_or_equal,between,not_between,begins_with,not_begins_with,contains,not_contains,ends_with,not_ends_with,is_empty,is_not_empty,is_null,is_not_null]
     *
     * @return $this
     */
    public function addOperator($operator) {
        $this->operators[] = $operator;

        return $this;
    }

    /**
     * @param array $operators [equal,not_equal,in,not_in,less,less_or_equal,greater,greater_or_equal,between,not_between,begins_with,not_begins_with,contains,not_contains,ends_with,not_ends_with,is_empty,is_not_empty,is_null,is_not_null]
     *
     * @return $this
     */
    public function setOperators(array $operators) {
        $this->operators = $operators;

        return $this;
    }

    public function setOperatorForString() {
        $operatorData = Constant::getOperatorData();
        $operators = c::collect($operatorData)->filter(function ($operator) {
            $applyTo = carr::get($operator, 'apply_to');
            $multiple = carr::get($operator, 'multiple');

            return in_array('string', $applyTo) && !$multiple;
        })->keys()->toArray();
        $this->operators = $operators;

        return $this;
    }

    public function setOperatorForNumber() {
        $operatorData = Constant::getOperatorData();
        $operators = c::collect($operatorData)->filter(function ($operator) {
            $applyTo = carr::get($operator, 'apply_to');
            $multiple = carr::get($operator, 'multiple');

            return in_array('number', $applyTo) && !$multiple;
        })->keys()->toArray();
        $this->operators = $operators;

        return $this;
    }

    public function setOperatorForDatetime() {
        $operatorData = Constant::getOperatorData();
        $operators = c::collect($operatorData)->filter(function ($operator) {
            $applyTo = carr::get($operator, 'apply_to');
            $multiple = carr::get($operator, 'multiple');

            return in_array('datetime', $applyTo) && !$multiple;
        })->keys()->toArray();
        $this->operators = $operators;

        return $this;
    }

    /**
     * @return $this
     */
    public function addOperatorEqual() {
        return $this->addOperator(Constant::FILTER_OPERATOR_EQUAL);
    }

    /**
     * @return $this
     */
    public function addOperatorNotEqual() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_EQUAL);
    }

    /**
     * @return $this
     */
    public function addOperatorIn() {
        return $this->addOperator(Constant::FILTER_OPERATOR_IN);
    }

    /**
     * @return $this
     */
    public function addOperatorNotIn() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_IN);
    }

    /**
     * @return $this
     */
    public function addOperatorLess() {
        return $this->addOperator(Constant::FILTER_OPERATOR_LESS);
    }

    /**
     * @return $this
     */
    public function addOperatorLessOrEqual() {
        return $this->addOperator(Constant::FILTER_OPERATOR_LESS_OR_EQUAL);
    }

    /**
     * @return $this
     */
    public function addOperatorGreater() {
        return $this->addOperator(Constant::FILTER_OPERATOR_GREATER);
    }

    /**
     * @return $this
     */
    public function addOperatorGreaterOrEqual() {
        return $this->addOperator(Constant::FILTER_OPERATOR_GREATER_OR_EQUAL);
    }

    /**
     * @return $this
     */
    public function addOperatorBetween() {
        return $this->addOperator(Constant::FILTER_OPERATOR_BETWEEN);
    }

    /**
     * @return $this
     */
    public function addOperatorNotBetween() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_BETWEEN);
    }

    /**
     * @return $this
     */
    public function addOperatorBeginsWith() {
        return $this->addOperator(Constant::FILTER_OPERATOR_BEGINS_WITH);
    }

    /**
     * @return $this
     */
    public function addOperatorNotBeginsWith() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_BEGINS_WITH);
    }

    /**
     * @return $this
     */
    public function addOperatorContains() {
        return $this->addOperator(Constant::FILTER_OPERATOR_CONTAINS);
    }

    /**
     * @return $this
     */
    public function addOperatorNotContains() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_CONTAINS);
    }

    /**
     * @return $this
     */
    public function addOperatorEndsWith() {
        return $this->addOperator(Constant::FILTER_OPERATOR_ENDS_WITH);
    }

    /**
     * @return $this
     */
    public function addOperatorNotEndsWith() {
        return $this->addOperator(Constant::FILTER_OPERATOR_NOT_ENDS_WITH);
    }

    /**
     * @return $this
     */
    public function addOperatorIsEmpty() {
        return $this->addOperator(Constant::FILTER_OPERATOR_IS_EMPTY);
    }

    /**
     * @return $this
     */
    public function addOperatorNotIsEmpty() {
        return $this->addOperator(Constant::FILTER_OPERATOR_IS_NOT_EMPTY);
    }

    /**
     * @return $this
     */
    public function addOperatoIsNull() {
        return $this->addOperator(Constant::FILTER_OPERATOR_IS_NULL);
    }

    /**
     * @return $this
     */
    public function addOperatoIsNotNull() {
        return $this->addOperator(Constant::FILTER_OPERATOR_IS_NOT_NULL);
    }

    /**
     * @param string $operator
     *
     * @return $this
     */
    public function setDefaultOperator($operator) {
        $this->defaultOperator = $operator;

        return $this;
    }
}

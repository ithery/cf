<?php

class CReport_Jasper_Report_Variable {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $calculation;

    /**
     * @var string
     */
    protected $variableExpression;

    /**
     * @var SimpleXMLElement
     */
    protected $xmlElement;

    /**
     * @var string
     */
    protected $initialValueExpression;

    /**
     * @var string
     */
    protected $resetType;

    /**
     * @var string
     */
    protected $resetGroup;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var string
     */
    protected $incrementType;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @var bool
     */
    protected $isValueSet;

    public function __construct($name, SimpleXMLElement $xmlElement = null) {
        $this->name = $name;
        $this->xmlElement = $xmlElement;
        $this->calculation = $xmlElement ? (string) $xmlElement['calculation'] : 'System';
        $this->variableExpression = $xmlElement ? (string) $xmlElement->variableExpression : '';
        $this->class = $xmlElement ? (string) $xmlElement['class'] : '';
        $this->resetType = $xmlElement ? (string) $xmlElement->resetType : '';
        $this->resetGroup = $xmlElement ? (string) $xmlElement->resetGroup : '';
        $this->initialValueExpression = $xmlElement ? (string) $xmlElement->initialValueExpression : '';
        $this->incrementType = $xmlElement ? (string) $xmlElement['incrementType'] : '';
        $this->isValueSet = false;
        $this->value = null;
    }

    /**
     * @return string
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getCalculation() {
        return $this->calculation;
    }

    /**
     * @return string
     */
    public function getVariableExpression() {
        return $this->variableExpression;
    }

    /**
     * @return string
     */
    public function getClass() {
        return $this->class;
    }

    /**
     * @return string
     */
    public function getResetType() {
        return $this->resetType;
    }

    /**
     * @return string
     */
    public function getResetGroup() {
        return $this->resetGroup;
    }

    /**
     * @return string
     */
    public function getInitialValueExpression() {
        return $this->initialValueExpression;
    }

    /**
     * @return string
     */
    public function getIncrementType() {
        return $this->incrementType;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->isValueSet ? $this->getInitialValue() : $this->value;
    }

    /**
     * @param mixed $value
     *
     * @return CReport_Jasper_Report_Variable
     */
    public function setValue($value) {
        $this->value = $value;
        $this->isValueSet = true;

        return $this;
    }

    /**
     * @return CReport_Jasper_Report_Variable
     */
    public function unsetValue() {
        $this->value = null;
        $this->isValueSet = false;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getInitialValue() {
        return $this->initialValueExpression;
    }
}

<?php

class CReport_Builder_Dictionary_Variable {
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
    protected $dataType;

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

    /**
     * @var CReport_Generator
     */
    protected $generator;

    public function __construct(CReport_Builder_Element_Variable $var, CReport_Generator $generator) {
        $this->generator = $generator;
        $this->name = $var->getName();
        $this->calculation = $var->getCalculation();
        $this->variableExpression = $var->getVariableExpression();
        $this->dataType = $var->getDataType();
        $this->resetType = $var->getResetType();
        $this->resetGroup = $var->getResetGroup();
        $this->initialValueExpression = $this->generator->getExpression($var->getInitialValueExpression());
        $this->incrementType = $var->getIncrementType();
        $this->isValueSet = false;
        $this->value = null;
    }

    public function setCalculation(string $calculation) {
        $this->calculation = $calculation;

        return $this;
    }

    public function setResetType(string $resetType) {
        $this->resetType = $resetType;
    }

    public function setResetGroup(string $resetGroup) {
        $this->resetGroup = $resetGroup;
    }

    public function setInitialValueExpression($initialValueExpression) {
        $this->initialValueExpression = $initialValueExpression;
    }

    public function setIncrementType($incrementType) {
        $this->incrementType = $incrementType;
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
    public function getDataType() {
        return $this->dataType;
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
        return $this->isValueSet ? $this->value : $this->getInitialValue();
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
        $initialValue = $this->initialValueExpression;
        if ($initialValue == null) {
            if ($this->dataType == CReport::DATA_TYPE_INT || $this->dataType == CReport::DATA_TYPE_FLOAT) {
                $initialValue = 0;
            }
            if ($this->dataType == CReport::DATA_TYPE_STRING) {
                $initialValue = '';
            }
        }

        return $initialValue;
    }
}

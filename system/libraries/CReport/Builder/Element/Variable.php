<?php

class CReport_Builder_Element_Variable extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;

    protected $variableExpression;

    protected $calculation;

    protected $incrementType;

    protected $initialValueExpression;

    protected $dataType;

    public function __construct() {
        parent::__construct();
        $this->name = null;
        $this->variableExpression = '';
        $this->calculation = CReport::CALCULATION_SYSTEM;
        $this->dataType = CReport::DATA_TYPE_FLOAT;
    }

    public function setVariableExpression($variableExpression) {
        $this->variableExpression = $variableExpression;

        return $this;
    }

    public function getVariableExpression() {
        return $this->variableExpression;
    }

    public function getDataType() {
        return $this->dataType;
    }

    public function setDataType($dataType) {
        $this->dataType = $dataType;

        return $this;
    }

    public function getCalculation() {
        return $this->calculation;
    }

    public function setCalculation($calculation) {
        $this->calculation = $calculation;

        return $this;
    }

    public function getInitialValueExpression() {
        return $this->initialValueExpression;
    }

    public function setInitialValueExpression($initialValueExpression) {
        $this->initialValueExpression = $initialValueExpression;

        return $this;
    }

    public function getIncrementType() {
        return $this->incrementType;
    }

    public function setincrementType($incrementType) {
        $this->incrementType = $incrementType;

        return $this;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<variable';
        if ($this->name) {
            $openTag .= ' name="' . $this->name . '"';
        }
        $openTag .= '>';

        $body = '';
        $closeTag = '</variable>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
    }
}

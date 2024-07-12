<?php

class CReport_Builder_Element_Variable extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;

    protected $variableExpression;

    protected $calculation;

    protected $incrementType;

    protected $initialValueExpression;

    protected $dataType;

    protected $resetType;

    protected $resetGroup;

    public function __construct() {
        parent::__construct();
        $this->name = null;
        $this->variableExpression = '';
        $this->calculation = CReport::CALCULATION_SYSTEM;
        $this->dataType = CReport::DATA_TYPE_FLOAT;
        $this->resetType = CReport::RESET_TYPE_REPORT;
    }

    /**
     * @param string $variableExpression
     *
     * @return $this
     */
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

    public function setIncrementType($incrementType) {
        $this->incrementType = $incrementType;

        return $this;
    }

    public function getResetType() {
        return $this->resetType;
    }

    public function setResetType($resetType) {
        $this->resetType = $resetType;

        return $this;
    }

    public function getResetGroup() {
        return $this->resetGroup;
    }

    public function setResetGroup($resetGroup) {
        $this->resetGroup = $resetGroup;

        return $this;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();
        if ($xml['name']) {
            $element->setName((string) $xml['name']);
        }
        if ($xml['class']) {
            $element->setDataType(CReport_Builder_JrXmlToPhpEnum::getPhpDataTypeEnum((string) $xml['class']));
        }
        if ($xml['calculation']) {
            $element->setCalculation(CReport_Builder_JrXmlToPhpEnum::getCalculationEnum((string) $xml['calculation']));
        }
        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'variableExpression') {
                $element->setVariableExpression((string) $xmlElement);
            }
            if ($tag == 'initialValueExpression') {
                $element->setInitialValueExpression((string) $xmlElement);
            }
            if ($tag == 'resetType') {
                $element->setResetType(CReport_Builder_JrXmlToPhpEnum::getResetTypeEnum((string) $xmlElement));
            }
            if ($tag == 'resetGroup') {
                $element->setResetGroup((string) $xmlElement);
            }
        }

        return $element;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<variable';
        if ($this->name) {
            $openTag .= ' name="' . $this->name . '"';
        }
        if ($this->dataType) {
            $openTag .= ' class="' . CReport_Builder_PhpToJrXmlEnum::getJavaDataTypeEnum($this->dataType) . '"';
        }
        if ($this->calculation) {
            $openTag .= ' calculation="' . CReport_Builder_PhpToJrXmlEnum::getCalculationEnum($this->calculation) . '"';
        }
        $openTag .= '>';

        $body = '';
        if ($this->variableExpression) {
            $body .= '<variableExpression><![CDATA[' . $this->variableExpression . ']]></variableExpression>' . PHP_EOL;
        }
        if ($this->initialValueExpression) {
            $body .= '<initialValueExpression><![CDATA[' . $this->initialValueExpression . ']]></initialValueExpression>' . PHP_EOL;
        }
        if ($this->resetType) {
            $body .= '<resetType>' . CReport_Builder_PhpToJrXmlEnum::getResetTypeEnum($this->resetType) . '</resetType>' . PHP_EOL;
        }
        if ($this->resetGroup) {
            $body .= '<resetGroup>' . $this->resetGroup . '</resetGroup>' . PHP_EOL;
        }

        $closeTag = '</variable>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
    }
}

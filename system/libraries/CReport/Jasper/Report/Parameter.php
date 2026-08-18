<?php

class CReport_Jasper_Report_Parameter {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SimpleXMLElement
     */
    protected $xmlElement;

    /**
     * @var string
     */
    protected $class;

    /**
     * @var mixed
     */
    protected $value;

    public function __construct($name, $value, SimpleXMLElement $xmlElement) {
        $this->name = $name;
        $this->xmlElement = $xmlElement;
        $this->class = (string) $xmlElement['class'];
        $this->value = $value;
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
    public function getClass() {
        return $this->class;
    }

    /**
     * @return mixed
     */
    public function getValue() {
        return $this->value;
    }
}

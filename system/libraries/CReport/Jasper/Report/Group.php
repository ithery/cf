<?php

class CReport_Jasper_Report_Group {
    /**
     * @var string
     */
    protected $name;

    /**
     * @var SimpleXMLElement
     */
    protected $xmlElement;

    /**
     * @var SimpleXMLElement
     */
    protected $groupFooter;

    /**
     * @var SimpleXMLElement
     */
    protected $groupHeader;

    /**
     * @var bool
     */
    protected $resetVariable;

    public function __construct($name, SimpleXMLElement $xmlElement) {
        $this->name = $name;
        $this->xmlElement = $xmlElement;
        $this->groupFooter = $xmlElement->groupFooter;
        $this->groupHeader = $xmlElement->groupHeader;
        $this->resetVariable = false;
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
    public function getGroupExpression() {
        return (string) $this->xmlElement->groupExpression;
    }

    /**
     * @return null|SimpleXmlElement
     */
    public function getGroupFooter() {
        return $this->hasGroupFooter() ? $this->groupFooter : null;
    }

    /**
     * @return bool
     */
    public function hasGroupFooter() {
        return (bool) $this->groupFooter;
    }

    /**
     * @return null|SimpleXmlElement
     */
    public function getGroupHeader() {
        return $this->hasGroupHeader() ? $this->groupHeader : null;
    }

    /**
     * @return bool
     */
    public function hasGroupHeader() {
        return (bool) $this->groupHeader;
    }

    /**
     * @return bool
     */
    public function isResetVariable() {
        return $this->resetVariable;
    }

    /**
     * @return CReport_Jasper_Report_Group
     */
    public function setResetVariable() {
        $this->resetVariable = true;

        return $this;
    }

    /**
     * @return CReport_Jasper_Report_Group
     */
    public function unsetResetVariable() {
        $this->resetVariable = false;

        return $this;
    }
}

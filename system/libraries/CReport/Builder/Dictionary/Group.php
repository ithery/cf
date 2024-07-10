<?php

class CReport_Builder_Dictionary_Group {
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

    public function __construct($name, SimpleXMLElement $xmlElement) {
        $this->name = $name;
        $this->xmlElement = $xmlElement;
        $this->groupFooter = $xmlElement->groupFooter;
        $this->groupHeader = $xmlElement->groupHeader;
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
}

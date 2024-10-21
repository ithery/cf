<?php

class CReport_Builder_Element_Group extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;

    protected $groupExpression;

    protected $isReprintHeaderOnEachPage;

    public function __construct() {
        parent::__construct();
        $this->name = null;
        $this->groupExpression = '';
        $this->isReprintHeaderOnEachPage = false;
    }

    public function setGroupExpression($groupExpression) {
        $this->groupExpression = $groupExpression;

        return $this;
    }

    public function setReprintHeaderOnEachPage($isReprintHeaderOnEachPage) {
        $this->isReprintHeaderOnEachPage = $isReprintHeaderOnEachPage;

        return $this;
    }

    public function isReprintHeaderOnEachPage() {
        return $this->isReprintHeaderOnEachPage;
    }

    public function getGroupExpression() {
        return $this->groupExpression;
    }

    /**
     * @return CReport_Builder_Element_GroupHeader
     */
    public function addGroupHeader() {
        $groupHeader = new CReport_Builder_Element_GroupHeader();
        $this->children[] = $groupHeader;

        return $groupHeader;
    }

    /**
     * @return CReport_Builder_Element_GroupFooter
     */
    public function addGroupFooter() {
        $groupFooter = new CReport_Builder_Element_GroupFooter();
        $this->children[] = $groupFooter;

        return $groupFooter;
    }

    /**
     * @return CCollection|CReport_Builder_Element_GroupHeader[]
     */
    public function getGroupHeaderElements() {
        return c::collect($this->children)->filter(function ($value) {
            return $value instanceof CReport_Builder_Element_GroupHeader;
        });
    }

    /**
     * @return CCollection|CReport_Builder_Element_GroupFooter[]
     */
    public function getGroupFooterElements() {
        return c::collect($this->children)->filter(function ($value) {
            return $value instanceof CReport_Builder_Element_GroupFooter;
        });
    }

    /**
     * @return bool
     */
    public function hasGroupHeader() {
        return $this->getGroupHeaderElements()->count() > 0;
    }

    /**
     * @return bool
     */
    public function hasGroupFooter() {
        return $this->getGroupFooterElements()->count() > 0;
    }

    /**
     * @return null|CReport_Builder_Element_GroupHeader
     */
    public function getGroupHeaderElement() {
        return $this->getGroupHeaderElements()->first();
    }

    /**
     * @return null|CReport_Builder_Element_GroupFooter
     */
    public function getGroupFooterElement() {
        return $this->getGroupFooterElements()->first();
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        if ($xml['name']) {
            $element->setName((string) $xml['name']);
        }
        if ($xml['isReprintHeaderOnEachPage']) {
            $element->setReprintHeaderOnEachPage(CReport_Builder_JrXmlToPhpEnum::getBoolEnum((string) $xml['isReprintHeaderOnEachPage']));
        }
        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'groupExpression') {
                $element->setGroupExpression((string) $xmlElement);
            }
        }
        $element->addChildrenFromXml($xml);

        return $element;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<group';
        if ($this->name) {
            $openTag .= ' name="' . $this->name . '"';
        }
        $openTag .= ' isReprintHeaderOnEachPage="' . CReport_Builder_PhpToJrXmlEnum::getBoolEnum($this->isReprintHeaderOnEachPage) . '"';
        $openTag .= '>';

        $body = '';
        if ($this->groupExpression) {
            $body .= '<groupExpression><![CDATA[' . $this->groupExpression . ']]></groupExpression>' . PHP_EOL;
        }
        $body .= $this->getChildrenJrXml();
        $closeTag = '</group>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
    }
}

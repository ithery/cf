<?php

class CReport_Builder_Element_Group extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->name = null;
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

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<group';
        if ($this->name) {
            $openTag .= ' name="' . $this->name . '"';
        }
        $openTag .= '>';

        $body = $this->getChildrenJrXml();
        $closeTag = '</group>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        parent::generate($generator, $processor);
    }
}

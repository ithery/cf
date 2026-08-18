<?php

class CReport_Builder_Element_Style extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;
    use CReport_Builder_Trait_Property_BoxPropertyTrait;
    use CReport_Builder_Trait_Property_BackgroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_ForegroundColorPropertyTrait;
    use CReport_Builder_Trait_Property_ModePropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->name = '';
        $this->box = null;
        $this->backgroundColor = null;
        $this->foregroundColor = null;
        $this->mode = CReport::MODE_OPAQUE;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();
        if ($xml['name']) {
            $element->setName((string) $xml['name']);
        }
        if ($xml['backcolor']) {
            $element->setBackgroundColor((string) $xml['backcolor']);
        }
        if ($xml['forecolor']) {
            $element->setForegroundColor((string) $xml['forecolor']);
        }
        if ($xml['mode']) {
            $element->setMode(CReport_Builder_JrXmlToPhpEnum::getModeEnum((string) $xml['mode']));
        }
        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'box') {
                $element->setBox(CReport_Builder_Object_Box::fromXml($xmlElement));
            }
        }

        return $element;
    }

    public function toJrXml() {
        $openTag = '<style';
        $openTag .= ' name="' . $this->name . '"';
        if ($this->foregroundColor !== null) {
            $openTag .= ' forecolor="' . $this->foregroundColor . '"';
        }
        if ($this->backgroundColor !== null) {
            $openTag .= ' backcolor="' . $this->backgroundColor . '"';
        }
        if ($this->mode !== null) {
            $openTag .= ' mode="' . CReport_Builder_PhpToJrXmlEnum::getModeEnum($this->mode) . '"';
        }
        $openTag .= '>';
        $body = '';
        if ($this->box) {
            $body .= $this->box->toJrXml();
        }
        $closeTag = '</style>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

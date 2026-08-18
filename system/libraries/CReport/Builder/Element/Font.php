<?php

class CReport_Builder_Element_Font extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_NamePropertyTrait;

    protected $path;

    public function __construct() {
        parent::__construct();
        $this->name = null;
        $this->path = null;
    }

    public function getPath() {
        return $this->path;
    }

    public function setPath($path) {
        $this->path = $path;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        $element->setName((string) $xml['name']);
        $element->setPath((string) $xml['path']);

        return $element;
    }

    public function toJrXml() {
        $tag = '<font';
        $tag .= 'name="' . $this->name . '"';
        $tag .= 'path="' . $this->path . '"';
        $tag .= '/>';

        return $tag;
    }
}

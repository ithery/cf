<?php

class CReport_Builder_Element_Band extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;

    public function __construct() {
        parent::__construct();
        $this->height = null;
    }

    public function toJrXml() {
        $openTag = '<band';
        if ($this->height !== null) {
            $openTag .= ' height="' . $this->height . '"';
        }
        $openTag .= '>';
        $body = $this->getChildrenJrXml();
        $closeTag = '</band>';

        return $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;
    }
}

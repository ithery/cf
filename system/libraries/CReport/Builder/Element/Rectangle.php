<?php

class CReport_Builder_Element_Rectangle extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;
    use CReport_Builder_Trait_HasGraphicElementTrait;

    protected $scaleImage;

    public function __construct() {
        parent::__construct();
        $this->height = null;
    }

    public static function fromXml(SimpleXMLElement $xml) {
        $element = new self();

        foreach ($xml as $tag => $xmlElement) {
            if ($tag == 'reportElement') {
                $element->setReportElementPropertyFromXml($xmlElement);
            }
            if ($tag == 'graphicElement') {
                $element->setGraphicElementPropertyFromXml($xmlElement);
            }
        }

        return $element;
    }

    /**
     * @return string
     */
    public function toJrXml() {
        $openTag = '<rectangle';
        $openTag .= '>';

        $reportElement = $this->getReportElementJrXml();
        $graphicElement = $this->getGraphicElementJrXml();

        $body = $reportElement . $graphicElement;
        $closeTag = '</rectangle>';
        $tag = $openTag . PHP_EOL . $body . PHP_EOL . $closeTag;

        return $tag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        if ($generator->evaluatePrintWhenExpression($this->printWhenExpression)) {
            $options = [];
            $options['x'] = $this->getX();
            $options['y'] = $this->getY();
            $options['width'] = $this->getWidth();
            $options['height'] = $this->getHeight();
            $options['pen'] = $this->getPen();
            $options['radius'] = $this->getRadius();
            $processor->rectangle($options);
        }
    }
}

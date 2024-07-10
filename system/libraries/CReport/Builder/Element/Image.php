<?php

class CReport_Builder_Element_Image extends CReport_Builder_ElementAbstract {
    use CReport_Builder_Trait_HasReportElementTrait;
    use CReport_Builder_Trait_Property_SrcPropertyTrait;
    use CReport_Builder_Trait_Property_HorizontalAlignmentPropertyTrait;
    use CReport_Builder_Trait_Property_VerticalAlignmentPropertyTrait;

    protected $scaleImage;

    public function __construct() {
        parent::__construct();
        $this->height = null;
        $this->verticalAlignment = CREPORT::VERTICAL_ALIGNMENT_TOP;
        $this->horizontalAlignment = CREPORT::HORIZONTAL_ALIGNMENT_LEFT;
        $this->scaleImage = CREPORT::SCALE_IMAGE_RETAIN_SHAPE;
    }

    /**
     * @see CReport
     *
     * @param mixed $scaleImage
     *
     * @return $this
     */
    public function setScaleImage($scaleImage) {
        $this->scaleImage = $scaleImage;

        return $this;
    }

    /**
     * @return string
     */
    public function getScaleImage() {
        return $this->scaleImage;
    }

    public function toJrXml() {
        // <reportElement x="20" y="0" width="779" height="100"/>
        //         <imageExpression><![CDATA["' . $headerImagePath . '"]]></imageExpression>
        $openTag = '<image';
        if ($this->horizontalAlignment) {
            $openTag .= ' hAlign="' . CReport_Builder_JrXmlEnum::getHorizontalAlignmentEnum($this->horizontalAlignment) . '"';
        }
        if ($this->verticalAlignment) {
            $openTag .= ' vAlign="' . CReport_Builder_JrXmlEnum::getHorizontalAlignmentEnum($this->verticalAlignment) . '"';
        }
        if ($this->scaleImage) {
            $openTag .= ' scaleImage="' . CReport_Builder_JrXmlEnum::getScaleImageEnum($this->verticalAlignment) . '"';
        }
        $openTag .= '>';

        $reportElement = $this->getReportElementJrXml();

        $imageExpression = '<imageExpression><![CDATA["' . $this->src . '"]]></imageExpression>';

        $body = $this->getChildrenJrXml();
        $closeTag = '</image>';
        $tag = $openTag . PHP_EOL . $reportElement . PHP_EOL . $imageExpression . PHP_EOL . $body . PHP_EOL . $closeTag;

        return $tag;
    }

    public function generate(CReport_Generator $generator, CReport_Generator_ProcessorAbstract $processor) {
        $options = [];
        $options['x'] = $this->getX();
        $options['y'] = $this->getY();
        $options['width'] = $this->getWidth();
        $options['height'] = $this->getHeight();
        $options['scaleImage'] = $this->getScaleImage();
        $options['horizontalAlignment'] = $this->getHorizontalAlignment();
        $options['verticalAlignment'] = $this->getVerticalAlignment();
        $options['src'] = $this->getSrc();
        $processor->image($options);
    }
}

<?php

trait CReport_Builder_Trait_HasBandElementTrait {
    use CReport_Builder_Trait_Property_HeightPropertyTrait;
    use CReport_Builder_Trait_Property_SplitTypePropertyTrait;

    public function jrXmlWrapWithBand(string $body) {
        $openTag = '<band';
        if ($this->height) {
            $openTag .= $this->height !== null ? ' height="' . $this->height . '"' : '';
        }

        if ($this->splitType) {
            $openTag .= $this->splitType !== null ? ' splitType="' . CReport_Builder_JrXmlEnum::getSplitTypeEnum($this->splitType) . '"' : '';
        }
        $openTag .= '>';
        $closeTag = '</band>';

        return $openTag . $body . $closeTag;
    }
}

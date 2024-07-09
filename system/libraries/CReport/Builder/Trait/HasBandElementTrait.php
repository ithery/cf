<?php

trait CReport_Builder_Trait_HasBandElementTrait {
    public function jrXmlWrapWithBand(string $body) {
        $openTag = '<band';
        if (c::hasTrait($this, CReport_Builder_Trait_Property_HeightPropertyTrait::class)) {
            $openTag .= $this->height !== null ? ' height="' . $this->height . '"' : '';
        }
        $openTag .= '>';
        $closeTag = '</band>';

        return $openTag . $body . $closeTag;
    }
}

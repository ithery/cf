<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_BodyComponent extends CEmail_Builder_Component {

    public function getStyles() {
        return [];
    }

    public function getShorthandAttrValue($attribute, $direction) {
        $componentAttributeDirection = $this->getAttribute($attribute . '-' . $direction);
        $componentAttribute = $this->getAttribute($attribute);

        if ($componentAttributeDirection) {
            return $componentAttributeDirection;
        }

        if (!$componentAttribute) {
            return 0;
        }

        return $this->shorthandParser($componentAttribute, $direction);
    }

    public function getShorthandBorderValue($direction) {
        $borderDirection = $direction && $this->getAttribute('border-' . $direction);
        $border = $this->getAttribute('border');

        return borderParser($borderDirection || $border || '0', 10);
    }

}

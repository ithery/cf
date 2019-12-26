<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

use CEmail_Builder_Helper as Helper;

class CEmail_Builder_Component_BodyComponent extends CEmail_Builder_Component {

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

        return Helper::shorthandParser($componentAttribute, $direction);
    }

    public function getShorthandBorderValue($direction) {
        $borderDirection = $direction && $this->getAttribute('border-' . $direction);
        $border = $this->getAttribute('border');
        
        return Helper::borderParser($borderDirection || $border || '0', 10);
    }

    public function htmlAttributes($attributes) {

        return carr::reduce($attributes, function($output, $v, $name) {
                    $value = $v;
                    if ($name == 'style') {
                        if (is_string($value)) {
                            $value = carr::get($this->getStyles(), $v);
                        }
                        $value = Helper::renderStyle($value);
                    }
                    if ($value != null && strlen($value) > 0) {
                        return $output . ' ' . $name . '="' . $value . '"';
                    }
                    return $output;
                }, '');
    }

    public function getBoxWidths() {
        $containerWidth = $this->context->getContainerWidth();
        //$parsedWidth = (int) $containerWidth;
        
        $widthParserResult = Helper::widthParser($containerWidth, ['parseFloatToInt' => false]);
        $unit = carr::get($widthParserResult, 'unit');
        $parsedWidth = carr::get($widthParserResult, 'parsedWidth');
        $paddings = $this->getShorthandAttrValue('padding', 'right') + $this->getShorthandAttrValue('padding', 'left');
        $borders = $this->getShorthandBorderValue('right') + $this->getShorthandBorderValue('left');


        return [
            'totalWidth' => $parsedWidth,
            'borders' => $borders,
            'paddings' => $paddings,
            'box' => $parsedWidth - $paddings - $borders,
        ];
    }

}

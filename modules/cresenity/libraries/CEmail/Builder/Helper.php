<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CEmail_Builder_Helper {

    public static function widthParser($width, $options = []) {
        $parseFloatToInt = carr::get($options, 'parseFloatToInt', true);

        $unitRegex = '/[\d.,]*(\D*)$/';
        $widthUnit = '';
        if (preg_match($unitRegex, $width, $matches)) {
            $widthUnit = $matches[1];
        }
        $parsedWidth = $width;
        switch ($widthUnit) {
            case '%':
                $parsedWidth = $parseFloatToInt ? floor($width) : $width;
                break;
            case 'px':
            default:
                $parsedWidth = floor($width);
                break;
        }
        if (strlen($widthUnit) == 0) {
            $widthUnit = 'px';
        }

        return [
            'parsedWidth' => $parsedWidth,
            'unit' => $widthUnit,
        ];
    }

    public static function suffixCssClasses($classes, $suffix) {
        if ($classes != null) {
            return implode(' ', carr::map(explode(' ', $clases), function($c) use($suffix) {
                        return $c . '-' . $suffix;
                    }));
        }
        return '';
    }

    public static function jsonToXML($tagName, $attributes, $children, $content) {
        $subNode = $content;
        if (is_array($children) && count($children) > 0) {
            $subNode = implode("\n", carr::map($children, function($child) {
                        //recursive here
                        return CEmail_Builder_Helper::jsonToXML($child->getTagName(), $child->getAttributes(), $children->getChildren(), $child->getContent());
                    }));
        }

        $attributesString = carr::reduce($attributes, function($output, $v, $name) {
                    if ($v != null && strlen($v) > 0) {
                        return $output . ' ' . $name . '="' . $v . '"';
                    }
                    return $output;
                }, '');

        return '<' . $tagName . $attributesString . '>' . $subNode . '</' . $tagName . '>';
    }

    public static function formatAttributes($attributes, $allowedAttributes) {
        return carr::reduce($attributes, function($acc, $val, $attrName) use($allowedAttributes) {
                    if (is_array($allowedAttributes) && isset($allowedAttributes[$attrName])) {
                        $typeClass = CEmail::builder()->determineTypeAdapter($allowedAttributes[$attrName]);
                        if ($typeClass) {
                            $typeAdapter = new $typeClass($allowedAttributes[$attrName], $val);

                            return array_merge($acc, [$attrName => $typeAdapter->getValue()]);
                        }
                    }

                    return array_merge($acc, [$attrName => $val]);
                }, []);
    }

    public static function renderStyle($styles) {
        if (!is_array($styles)) {
            $styles = array($styles);
        }
        return carr::reduce($styles, function($output, $value, $name) {
                    if ($value !== null && strlen($value) > 0) {
                        return $output . $name . ':' . $value . ';';
                    }
                    return $output;
                }, '');
    }

    public static function conditionalTag($content, $negation = false) {
        $startConditionalTag = '<!--[if mso | IE]>';
        $startNegationConditionalTag = '<!--[if !mso | IE]><!-->';
        $endConditionalTag = '<![endif]-->';
        $endNegationConditionalTag = '<!--<![endif]-->';
        return ($negation ? $startNegationConiditonalTag : $startConditionalTag) . $content . ($negation ? $endNegationConditionalTag : $endConditionalTag);
    }

    public static function msoConditionalTag($content, $negation = false) {
        $startMsoConditionalTag = '<!--[if mso]>';
        $startMsoNegationConditionalTag = '<!--[if !mso><!-->';
        $endConditionalTag = '<![endif]-->';
        $endNegationConditionalTag = '<!--<![endif]-->';
        return ($negation ? $startMsoNegationConditionalTag : $startMsoConditionalTag) . $content . ($negation ? $endNegationConditionalTag : $endConditionalTag);
    }

}

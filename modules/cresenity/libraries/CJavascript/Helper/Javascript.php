<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 12:52:12 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CJavascript_Helper_Javascript {

    public static $preventDefault = "\nif(event && event.preventDefault) event.preventDefault();\n";
    public static $stopPropagation = "\nif(event && event.stopPropagation) event.stopPropagation();\n";

    public static function draggable($attr = "id") {
        return 'var dt=event.dataTransfer || event.originalEvent.dataTransfer;dt.setData("text/plain",JSON.stringify({id:$(event.target).attr("id"),data:$(event.target).attr("' . $attr . '")}));';
    }

    public static function dropZone($jqueryDone, $jsCallback = "") {
        return 'var dt=event.dataTransfer || event.originalEvent.dataTransfer;var _data=JSON.parse(dt.getData("text/plain"));$(event.target).' . $jqueryDone . '($("#"+_data.id));var data=_data.data;' . $jsCallback;
    }

    public static function containsCode($expression) {
        return strrpos($expression, 'this') !== false || strrpos($expression, 'event') !== false || strrpos($expression, 'self') !== false;
    }

    public static function isFunction($jsCode) {
        return CJavascript_Helper_String::startswith($jsCode, "function");
    }

    /**
     * Puts HTML element in quotes for use in jQuery code
     * unless the supplied element is the Javascript 'this'
     * object, in which case no quotes are added
     *
     * @param string $element
     * @return string
     */
    public static function prepElement($element) {
        if (self::containsCode($element) === false) {
            $element = '"' . addslashes($element) . '"';
        }
        return $element;
    }

    /**
     * Puts HTML values in quotes for use in jQuery code
     * unless the supplied value contains the Javascript 'this' or 'event'
     * object, in which case no quotes are added
     *
     * @param string $value
     * @return string
     */
    public static function prepValue($value) {
        if (\is_array($value)) {
            $value = implode(",", $value);
        }
        if (self::containsCode($value) === false) {
            $value = \str_replace(["\\", "\""], ["\\\\", "\\\""], $value);
            $value = '"' . $value . '"';
        }
        return trim($value, "%");
    }

    public static function prepJQuerySelector($value) {
        if (CJavascript_Helper_String::startswith($value, '$(') === false) {
            return '$(' . self::prepValue($value) . ')';
        }
        return $value;
    }

}

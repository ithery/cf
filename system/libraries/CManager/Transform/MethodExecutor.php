<?php

class CManager_Transform_MethodExecutor {
    protected $method;

    public function __construct($method) {
        $this->method = $method;
    }

    public function transform($value, $args = []) {
        if ($this->method instanceof CManager_Transform_Contract_TransformMethodInterface) {
            return $this->method->transform($value, $args);
        }
        $method = 'transform' . $this->method;
        if (method_exists($this, $method)) {
            return call_user_func_array([$this, $method], array_merge([$value], $args));
        }

        return $value;
    }

    public function transformFormatDate($value, $format = null) {
        return c::formatter()->formatDate($value, $format);
    }

    public function transformUnformatDate($value, $format = null) {
        return c::formatter()->unformatDate($value, $format);
    }

    public function transformFormatDatetime($value, $format = null) {
        return c::formatter()->formatDatetime($value, $format);
    }

    public function transformUnformatDatetime($value, $format = null) {
        return c::formatter()->unformatDatetime($value, $format);
    }

    public function transformCarbonDiffForHuman($value) {
        return CCarbon::parse($value)->diffForHumans();
    }

    public function transformFormatNumber($value, $decimalSeparator = null, $thousandSeparator = null) {
        return c::formatter()->formatNumber($value, $decimalSeparator, $thousandSeparator);
    }

    public function transformFormatDecimal($value, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null) {
        return c::formatter()->formatDecimal($value, $decimalDigit, $decimalSeparator, $thousandSeparator);
    }

    public function transformThousandSeparator($value, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null) {
        return c::formatter()->formatDecimal($value, $decimalDigit, $decimalSeparator, $thousandSeparator, true);
    }

    public function transformFormatCurrency($value, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $currencyPrefix = null, $currencySuffix = null, $stripZeroDecimal = null) {
        return c::formatter()->formatCurrency($value, $decimalDigit, $decimalSeparator, $thousandSeparator, $currencyPrefix, $currencySuffix, $stripZeroDecimal);
    }

    public function transformUnformatCurrency($value) {
        return c::formatter()->unformatCurrency($value);
    }

    public function transformUppercase($value) {
        return cstr::upper($value);
    }

    public function transformLowercase($value) {
        return cstr::lower($value);
    }

    public function transformYesNo($value) {
        return $value ? 'YES' : 'NO';
    }

    public function transformEscape($value) {
        return c::e($value);
    }

    public function transformAscii($value) {
        return cstr::ascii($value);
    }

    public function transformJson($value) {
        return json_encode($value, JSON_PRETTY_PRINT);
    }

    public function transformShowMore($value, $limit = 100) {
        if (is_string($value) && strlen($value) > $limit) {
            return CElement_Component_ShowMore::factory()->setLimit($limit)->add($value);
        }

        return $value;
    }

    public function transformDiv($value, ...$classes) {
        $classes = implode(' ', $classes);
        if ($value instanceof CRenderable) {
            return CElement_Element_Div::factory()->addClass($classes)->add($value);
        }

        return '<div class="' . $classes . '">' . $value . '</div>';
    }

    public function transformSpan($value, ...$classes) {
        $classes = implode(' ', $classes);
        if ($value instanceof CRenderable) {
            return CElement_Element_Span::factory()->addClass($classes)->add($value);
        }

        return '<span class="' . $classes . '">' . $value . '</span>';
    }

    /**
     * Format bytes to kb, mb, gb, tb.
     *
     * @param int $size
     * @param int $precision
     *
     * @return string
     */
    public static function transformFormatByte($size, $precision = 2) {
        if ($size <= 0) {
            return (string) $size;
        }

        $base = log($size) / log(1024);
        $suffixes = [' bytes', ' KB', ' MB', ' GB', ' TB'];

        return round(1024 ** ($base - floor($base)), $precision) . $suffixes[(int) floor($base)];
    }

    /**
     * Format bytes to kb, mb, gb, tb.
     *
     * @param int $meters
     * @param int $precision
     *
     * @return string
     */
    public static function transformFormatDistance($meters, $precision = 2) {
        if ($meters >= 1000) {
            $meters /= 1000;
            $units = 'km';
        } else {
            $precision = 0;
            $units = 'm';
        }

        return c::formatter()->formatDecimal($meters, $precision, null, null, true) . ' ' . $units;
    }

    public static function transformMonthName($month) {
        $list = [
            1 => 'January',
            2 => 'February',
            3 => 'March',
            4 => 'April',
            5 => 'May',
            6 => 'June',
            7 => 'July',
            8 => 'August',
            9 => 'September',
            10 => 'October',
            11 => 'November',
            12 => 'December'
        ];

        if (isset($list[$month])) {
            return $list[$month];
        }

        return 'Unknown';
    }

    public static function transformEditorJsHtml($data) {
        return c::manager()->editorJs()->generateHtmlOutput($data)->__toString();
    }

    public static function transformLink($value, $url) {
    }
}

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

    public function transformFormatNumber($value, $decimalSeparator = null, $thousandSeparator = null) {
        return c::formatter()->formatNumber($value, $decimalSeparator, $thousandSeparator);
    }

    public function transformFormatDecimal($value, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null) {
        return c::formatter()->formatDecimal($value, $decimalDigit, $decimalSeparator, $thousandSeparator);
    }

    public function transformFormatCurrency($value, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $currencyPrefix = null, $currencySuffix = null) {
        return c::formatter()->formatDecimal($value, $decimalDigit, $decimalSeparator, $thousandSeparator, $currencyPrefix, $currencySuffix);
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

    public function transformHtmlSpecialChars($value) {
        return c::e($value);
    }
}

<?php

/**
 * This class for utilize application format.
 */
class CApp_Formatter {
    protected $dateFormat;

    protected $datetimeFormat;

    protected $thousandSeparator;

    protected $decimalSeparator;

    protected $decimalDigit;

    protected $currencyDecimalDigit;

    protected $currencyPrefix;

    protected $currencySuffix;

    private static $instance;

    public static function instance() {
        if (static::$instance == null) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    private function __construct() {
        $this->dateFormat = CF::config('app.format.date', CF::config('date_formatted', 'Y-m-d'));
        $this->datetimeFormat = CF::config('app.format.datetime', CF::config('long_date_formatted', 'Y-m-d H:i:s'));
        $this->thousandSeparator = CF::config('app.format.thousand_separator', '.');
        $this->decimalSeparator = CF::config('app.format.decimal_separator', ',');
        $this->decimalDigit = CF::config('app.format.decimal_digit', 0);
        $this->currencyDecimalDigit = CF::config('app.format.currency_decimal_digit', $this->decimalDigit);
        $this->currencyPrefix = CF::config('app.format.currency_prefix', '');
        $this->currencySuffix = CF::config('app.format.currency_suffix', '');
    }

    public function getDateFormat() {
        return $this->dateFormat;
    }

    public function setDateFormat($format) {
        $this->dateFormat = $format;

        return $this;
    }

    public function getDatetimeFormat() {
        return $this->datetimeFormat;
    }

    public function setDatetimeFormat($format) {
        $this->datetimeFormat = $format;

        return $this;
    }

    public function getThousandSeparator() {
        return $this->thousandSeparator;
    }

    public function setThousandSeparator($thousandSeparator) {
        $this->thousandSeparator = $thousandSeparator;

        return $this;
    }

    public function getDecimalSeparator() {
        return $this->decimalSeparator;
    }

    public function setDecimalSeparator($decimalSeparator) {
        $this->decimalSeparator = $decimalSeparator;

        return $this;
    }

    public function getDecimalDigit() {
        return $this->decimalDigit;
    }

    public function setDecimalDigit($decimalDigit) {
        $this->decimalDigit = $decimalDigit;

        return $this;
    }

    public function formatDate($x) {
        if (strlen($x) == 0) {
            return $x;
        }
        $dateFormat = $this->dateFormat;
        if (strlen($dateFormat) == 0) {
            return $x;
        }

        return date($dateFormat, strtotime($x));
    }

    public function unformatDate($x) {
        return date('Y-m-d', strtotime($x));
    }

    public function formatDatetime($x) {
        if (strlen($x) == 0) {
            return $x;
        }
        $datetimeFormat = $this->datetimeFormat;
        if (strlen($datetimeFormat) == 0) {
            return $x;
        }

        return date($datetimeFormat, strtotime($x));
    }

    public function unformatDatetime($x) {
        return date('Y-m-d H:i:s', strtotime($x));
    }

    public function formatCurrency($x) {
        $x = number_format($x, $this->currencyDecimalDigit, $this->decimalSeparator, $this->thousandSeparator);

        return $this->currencyPrefix . $x . $this->currencySuffix;
    }

    public function formatNumber($x) {
        return number_format($x, 0, $this->decimalSeparator, $this->thousandSeparator);
    }

    public function formatDecimal($x) {
        return number_format($x, $this->decimalDigit, $this->decimalSeparator, $this->thousandSeparator);
    }

    public function unformatCurrency($number) {
        $number = preg_replace('/^[^\d]+/', '', $number);

        $type = (strpos($number, $this->decimalSeparator) === false) ? 'int' : 'float';
        $number = str_replace([$this->decimalSeparator, $this->thousandSeparator], ['.', ''], $number);
        settype($number, $type);

        return $number;
    }
}

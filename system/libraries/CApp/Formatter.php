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

    public function formatDate($x, $format = null) {
        if (strlen($x) == 0) {
            return $x;
        }
        $dateFormat = $format ?: $this->dateFormat;
        if (strlen($dateFormat) == 0) {
            return $x;
        }
        $carbon = CCarbon::parse($x);
        if ($carbon instanceof \CarbonV3\Carbon) {
            return $carbon->translatedFormat($dateFormat);
        }

        return $carbon->format($dateFormat);
    }

    public function unformatDate($x, $fromFormat = null) {
        $dateFormat = $fromFormat ?: $this->dateFormat;
        $date = CCarbon::createFromLocaleFormat($dateFormat, CCarbon::getLocale(), $x);

        return $date->format('Y-m-d');
    }

    public function formatDatetime($x, $format = null) {
        if (strlen($x) == 0) {
            return $x;
        }
        $datetimeFormat = $format ?: $this->datetimeFormat;
        if (strlen($datetimeFormat) == 0) {
            return $x;
        }

        $carbon = CCarbon::parse($x);
        if ($carbon instanceof \CarbonV3\Carbon) {
            return $carbon->translatedFormat($datetimeFormat);
        }

        return $carbon->format($datetimeFormat);
    }

    public function unformatDatetime($x, $fromFormat = null) {
        $datetimeFormat = $fromFormat ?: $this->datetimeFormat;
        $date = CCarbon::createFromLocaleFormat($datetimeFormat, CCarbon::getLocale(), $x);

        return $date->format('Y-m-d H:i:s');
    }

    public function formatCurrency($x, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $currencyPrefix = null, $currencySuffix = null, $stripZeroDecimal = false) {
        $decimalSeparator = $decimalSeparator ?: $this->decimalSeparator;
        $thousandSeparator = $thousandSeparator ?: $this->thousandSeparator;
        $decimalDigit = $decimalDigit ?: $this->decimalDigit;
        $currencySuffix = $currencySuffix ?: $this->currencySuffix;
        $currencyPrefix = $currencyPrefix ?: $this->currencyPrefix;

        $x = number_format((float) $x, $decimalDigit, $decimalSeparator, $thousandSeparator);
        if ($stripZeroDecimal) {
            if (substr($x, ($decimalDigit + 1) * -1) === '.' . cstr::repeat('0', $decimalDigit)) {
                $x = substr($x, 0, ($decimalDigit + 1) * -1);
            }
        }

        return $currencyPrefix . $x . $currencySuffix;
    }

    public function formatNumber($x, $decimalSeparator = null, $thousandSeparator = null) {
        return $this->formatDecimal($x, 0, $decimalSeparator, $thousandSeparator);
    }

    public function formatDecimal($x, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $stripZeroDecimal = false) {
        return $this->formatCurrency($x, $decimalDigit, $decimalSeparator, $thousandSeparator, '', '', $stripZeroDecimal);
    }

    public function unformatCurrency($number) {
        $number = preg_replace('/^[^\d]+/', '', $number);

        $type = (strpos($number, $this->decimalSeparator) === false) ? 'int' : 'float';
        $number = str_replace([$this->decimalSeparator, $this->thousandSeparator], ['.', ''], $number);
        settype($number, $type);

        return $number;
    }

    public function unformatNumber($number) {
        return $this->unformatCurrency($number);
    }
}

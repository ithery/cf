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

    protected $currencyStripZeroDecimal;

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
        $this->currencyDecimalDigit = CF::config('app.format.currency_decimal_digit', null);
        $this->currencyPrefix = CF::config('app.format.currency_prefix', '');
        $this->currencySuffix = CF::config('app.format.currency_suffix', '');
        $this->currencyStripZeroDecimal = CF::config('app.format.currency_strip_zero_decimal', false);
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

    public function setCurrencyDecimalDigit($decimalDigit) {
        $this->currencyDecimalDigit = $decimalDigit;

        return $this;
    }

    /**
     * @return int
     */
    public function getCurrencyDecimalDigit() {
        return $this->currencyDecimalDigit;
    }

    public function setCurrencyStripZeroDecimal($isStriped = true) {
        $this->currencyStripZeroDecimal = $isStriped;

        return $this;
    }

    /**
     * @return bool
     */
    public function getCurrencyStripZeroDecimal() {
        return $this->currencyStripZeroDecimal;
    }

    public function setCurrencyPrefix($prefix) {
        $this->currencyPrefix = $prefix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencyPrefix() {
        return $this->currencyPrefix;
    }

    public function setCurrencySuffix($suffix) {
        $this->currencySuffix = $suffix;

        return $this;
    }

    /**
     * @return string
     */
    public function getCurrencySuffix() {
        return $this->currencySuffix;
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

        return $carbon->translatedFormat($dateFormat);
    }

    public function unformatDate($x, $fromFormat = null) {
        $dateFormat = $fromFormat ?: $this->dateFormat;

        try {
            $date = CCarbon::createFromLocaleFormat($dateFormat, CCarbon::getLocale(), $x);
        } catch (Exception $ex) {
            $date = CCarbon::parse($x);
        }

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

        return $carbon->translatedFormat($datetimeFormat);
    }

    public function unformatDatetime($x, $fromFormat = null) {
        if ($x instanceof DateTime) {
            return $x->format('Y-m-d H:i:s');
        }
        $datetimeFormat = $fromFormat ?: $this->datetimeFormat;
        $date = CCarbon::createFromLocaleFormat($datetimeFormat, CCarbon::getLocale(), $x);

        return $date->format('Y-m-d H:i:s');
    }

    public function formatCurrency($x, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $currencyPrefix = null, $currencySuffix = null, $stripZeroDecimal = null) {
        $decimalSeparator = $decimalSeparator ?: $this->decimalSeparator;
        $thousandSeparator = $thousandSeparator ?: $this->thousandSeparator;
        $decimalDigit = $decimalDigit ?: ($this->currencyDecimalDigit ?: $this->decimalDigit);
        $currencySuffix = $currencySuffix ?: $this->currencySuffix;
        $currencyPrefix = $currencyPrefix ?: $this->currencyPrefix;
        $stripZeroDecimal = $stripZeroDecimal !== null ? $stripZeroDecimal : $this->currencyStripZeroDecimal;

        $x = number_format((float) $x, $decimalDigit, $decimalSeparator, $thousandSeparator);
        if ($stripZeroDecimal) {
            if (substr($x, ($decimalDigit + 1) * -1) === $decimalSeparator . cstr::repeat('0', $decimalDigit)) {
                $x = substr($x, 0, ($decimalDigit + 1) * -1);
            }
            if (strpos($x, $decimalSeparator) !== false) {
                $x = rtrim($x, '0');
            }
        }

        return $currencyPrefix . $x . $currencySuffix;
    }

    public function formatNumber($x, $decimalSeparator = null, $thousandSeparator = null) {
        return $this->formatDecimal($x, 0, $decimalSeparator, $thousandSeparator, true);
    }

    public function formatDecimal($x, $decimalDigit = null, $decimalSeparator = null, $thousandSeparator = null, $stripZeroDecimal = false) {
        $decimalDigit = $decimalDigit ?: $this->decimalDigit;

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

    public function formatSize($bytes) {
        $si_prefix = ['B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];
        $base = 1024;
        $class = min((int) log($bytes, $base), count($si_prefix) - 1);
        if (pow($base, $class) == 0) {
            return 0;
        }

        return sprintf('%1.2f', $bytes / pow($base, $class)) . ' ' . $si_prefix[$class];
    }
}

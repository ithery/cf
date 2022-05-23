<?php

class CString_Formatter_CurrencyFormatter {
    protected $thousandSeparator;

    protected $decimalSeparator;

    protected $decimalDigit;

    protected $prefix;

    protected $suffix;

    public function __construct(
        $thousandSeparator = ',',
        $decimalSeparator = '.',
        $decimalDigit = 0,
        $prefix = '',
        $suffix = ''
    ) {
        $this->thousandSeparator = $thousandSeparator;
        $this->decimalSeparator = $decimalSeparator;
        $this->decimalDigit = $decimalDigit;
        $this->prefix = $prefix;
        $this->suffix;
    }

    public function format($x) {
        $x = number_format($x, $this->decimalDigit, $this->decimalSeparator, $this->thousandSeparator);

        return $this->prefix . $x . $this->suffix;
    }

    public function unformat($x) {
        $cleanString = preg_replace('/([^0-9\.,])/i', '', $x);
        $onlyNumbersString = preg_replace('/([^0-9])/i', '', $x);

        $separatorsCountToBeErased = strlen($cleanString) - strlen($onlyNumbersString) - 1;

        $stringWithCommaOrDot = preg_replace('/([,\.])/', '', $cleanString, $separatorsCountToBeErased);
        $removedThousandSeparator = preg_replace('/(\.|,)(?=[0-9]{3,}$)/', '', $stringWithCommaOrDot);

        return (float) str_replace(',', '.', $removedThousandSeparator);
    }
}

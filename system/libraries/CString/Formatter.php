<?php

class CString_Formatter {
    public static function currency(
        $thousandSeparator = ',',
        $decimalSeparator = '.',
        $decimalDigit = 0,
        $prefix = '',
        $suffix = ''
    ) {
        return new CString_Formatter_CurrencyFormatter(
            $thousandSeparator,
            $decimalSeparator,
            $decimalDigit,
            $prefix,
            $suffix
        );
    }
}

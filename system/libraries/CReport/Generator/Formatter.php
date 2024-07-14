<?php

class CReport_Generator_Formatter {
    public static $decimalSeparator = '.';

    public static $thousandSeparator = ',';

    public static function formatPattern($txt, $pattern) {
        if ($txt != '') {
            $nome_meses = ['Janeiro', 'Janeiro', 'Fevereiro', 'Marco', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'];
            if (substr($pattern, 0, 1) === '%') {
                return sprintf($pattern, $txt);
            } elseif ($pattern == '###0') {
                return number_format($txt, 0, '', '');
            } elseif ($pattern == '#.##0') {
                return number_format($txt, 0, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '###0.0') {
                return number_format($txt, 1, self::$decimalSeparator, '');
            } elseif ($pattern == '#,##0.0' || $pattern == '#,##0.0;-#,##0.0') {
                if (!is_numeric($txt)) {
                    $txt = 0;
                }

                return number_format($txt, 1, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '###0.00' || $pattern == '###0.00;-###0.00') {
                return number_format($txt, 2, self::$decimalSeparator, '');
            } elseif ($pattern == '#,##0.00' || $pattern == '#,##0.00;-#,##0.00') {
                if (!is_numeric($txt)) {
                    $txt = 0;
                }
                $result = number_format($txt, 2, self::$decimalSeparator, self::$thousandSeparator);
                cdbg::d($txt, $result);

                return $result;
            } elseif ($pattern == '###0.00;(###0.00)') {
                return $txt < 0 ? '(' . number_format(abs($txt), 2, self::$decimalSeparator, '') . ')' : number_format($txt, 2, self::$decimalSeparator, '');
            } elseif ($pattern == '#,##0.00;(#,##0.00)') {
                return $txt < 0 ? '(' . number_format(abs($txt), 2, self::$decimalSeparator, self::$thousandSeparator) . ')' : number_format($txt, 2, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '#,##0.00;(-#,##0.00)') {
                return $txt < 0 ? '(' . number_format($txt, 2, self::$decimalSeparator, self::$thousandSeparator) . ')' : number_format($txt, 2, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '###0.000') {
                return number_format($txt, 3, self::$decimalSeparator, '');
            } elseif ($pattern == '#,##0.000') {
                return number_format($txt, 3, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '#,##0.0000') {
                return number_format($txt, 4, self::$decimalSeparator, self::$thousandSeparator);
            } elseif ($pattern == '###0.0000') {
                return number_format($txt, 4, self::$decimalSeparator, '');
            } elseif ($pattern == '#,##0') {
                // start latin formats
                return number_format($txt, 0, '.', ',');
            } elseif ($pattern == '###0,0') {
                return number_format($txt, 1, ',', '');
            } elseif ($pattern == '#.##0,0' || $pattern == '#.##0,0;-#.##0,0') {
                return number_format($txt, 1, ',', '.');
            } elseif ($pattern == '###0,00' || $pattern == '###0,00;-###0,00') {
                return number_format($txt, 2, ',', '');
            } elseif ($pattern == '#.##0,00' || $pattern == '#.##0,00;-#.##0,00') {
                return number_format($txt, 2, ',', '.');
            } elseif ($pattern == '###0,00;(###0,00)') {
                return $txt < 0 ? '(' . number_format(abs($txt), 2, ',', '') . ')' : number_format($txt, 2, ',', '');
            } elseif ($pattern == '#.##0,00;(#.##0,00)') {
                return $txt < 0 ? '(' . number_format(abs($txt), 2, ',', '.') . ')' : number_format($txt, 2, ',', '.');
            } elseif ($pattern == '#.##0,00;(-#.##0,00)') {
                return $txt < 0 ? '(' . number_format($txt, 2, ',', '.') . ')' : number_format($txt, 2, ',', '.');
            } elseif ($pattern == '###0,000') {
                return number_format($txt, 3, ',', '');
            } elseif ($pattern == '#.##0,000') {
                return number_format($txt, 3, ',', '.');
            } elseif ($pattern == '#.##0,0000') {
                return number_format($txt, 4, ',', '.');
            } elseif ($pattern == '###0,0000') {
                return number_format($txt, 4, ',', '');
            } elseif ($pattern == 'xx/xx' && $txt != '') {
                return mb_substr($txt, 0, 2) . '/' . mb_substr($txt, 2, 2);
            } elseif ($pattern == 'xx.xx' && $txt != '') {
                return mb_substr($txt, 0, 2) . '.' . mb_substr($txt, 2, 2);
            } elseif (($pattern == 'dd/MM/yyyy' || $pattern == 'ddMMyyyy') && $txt != '') {
                return date('d/m/Y', strtotime($txt));
            } elseif ($pattern == 'MM/dd/yyyy' && $txt != '') {
                return date('m/d/Y', strtotime($txt));
            } elseif ($pattern == 'dd/MM/yy' && $txt != '') {
                return date('d/m/y', strtotime($txt));
            } elseif ($pattern == 'yyyy/MM/dd' && $txt != '') {
                return date('Y/m/d', strtotime($txt));
            } elseif ($pattern == 'dd-MMM-yy' && $txt != '') {
                return date('d-M-Y', strtotime($txt));
            } elseif ($pattern == 'dd-MMM-yy' && $txt != '') {
                return date('d-M-Y', strtotime($txt));
            } elseif ($pattern == 'dd/MM/yyyy h.mm a' && $txt != '') {
                return date('d/m/Y h:i a', strtotime($txt));
            } elseif ($pattern == 'dd/MM/yyyy HH.mm.ss' && $txt != '') {
                return date('d-m-Y H:i:s', strtotime($txt));
            } elseif (($pattern == 'dd/MM/yyyy HH:mm' || $pattern == 'dd/MM/yyyy HH.mm' || $pattern == 'dd/MM/yyyy H:m') && $txt != '') {
                return date('d/m/Y H:i', strtotime($txt));
            } elseif ($pattern == 'H:m:s' && $txt != '') {
                return date('H:i:s', strtotime($txt));
            } elseif (($pattern == 'H:m' || $pattern == 'HH:mm' || $pattern == 'H.m' || $pattern == 'HH.mm') && $txt != '') {
                return date('H:i', strtotime($txt));
            } elseif (($pattern == 'dFyyyy') && $txt != '') {
                return date('d ', strtotime($txt)) . ' de ' . $nome_meses[date('n', strtotime($txt))] . ' de ' . date('Y', strtotime($txt));
            } elseif (($pattern == 'dFbyyyy') && $txt != '') {
                return date('d', strtotime($txt)) . '/' . $nome_meses[date('n', strtotime($txt))] . '/' . date('Y', strtotime($txt));
            } elseif (($pattern == 'dFByyyy') && $txt != '') {
                return date('d', strtotime($txt)) . '/' . mb_strtoupper($nome_meses[date('n', strtotime($txt))]) . '/' . date('Y', strtotime($txt));
            } elseif ($pattern != '' && $txt != '') {
                return date($pattern, strtotime($txt));
            } else {
                return $txt;
            }
        } else {
            return $txt;
        }
    }
}

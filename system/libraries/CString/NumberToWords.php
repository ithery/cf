<?php
/**
 * @see CString
 */
class CString_NumberToWords {
    public static function toWords($number, $locale = null) {
        if ($locale == null) {
            $locale = CF::getLocale();
        }
        $defaultClass = CString_NumberToWords_EnglishNumberToWords::class;
        $localeMap = [
            'id_ID' => CString_NumberToWords_IndonesianNumberToWords::class,
            'en_US' => CString_NumberToWords_EnglishNumberToWords::class,

        ];
        $class = carr::get($localeMap, $locale, $defaultClass);

        return $class::toWords($number);
    }
}

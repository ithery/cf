<?php

class CEmail_Client_Utils {
    const CHARSET_UTF_8 = 'utf-8';

    const CHARSET_UTF_7 = 'utf-7';

    const CHARSET_UTF_7_IMAP = 'utf7-imap';

    const CHARSET_WIN_1250 = 'windows-1250';

    const CHARSET_WIN_1251 = 'windows-1251';

    const CHARSET_WIN_1252 = 'windows-1252';

    const CHARSET_WIN_1253 = 'windows-1253';

    const CHARSET_WIN_1254 = 'windows-1254';

    const CHARSET_WIN_1255 = 'windows-1255';

    const CHARSET_WIN_1256 = 'windows-1256';

    const CHARSET_WIN_1257 = 'windows-1257';

    const CHARSET_WIN_1258 = 'windows-1258';

    const CHARSET_ISO_8859_1 = 'iso-8859-1';

    const CHARSET_ISO_8859_8 = 'iso-8859-8';

    const CHARSET_ISO_8859_8_I = 'iso-8859-8-i';

    const CHARSET_ISO_2022_JP = 'iso-2022-jp';

    const ENCODING_QUOTED_PRINTABLE = 'Quoted-Printable';

    const ENCODING_QUOTED_PRINTABLE_LOWER = 'quoted-printable';

    const ENCODING_QUOTED_PRINTABLE_SHORT = 'Q';

    const ENCODING_BASE64 = 'Base64';

    const ENCODING_BASE64_LOWER = 'base64';

    const ENCODING_BASE64_SHORT = 'B';

    const ENCODING_SEVEN_BIT = '7bit';

    const ENCODING_7_BIT = '7bit';

    const ENCODING_EIGHT_BIT = '8bit';

    const ENCODING_8_BIT = '8bit';

    /**
     * @var array
     */
    public static $suppostedCharsets = [
        'iso-8859-1', 'iso-8859-2', 'iso-8859-3', 'iso-8859-4', 'iso-8859-5', 'iso-8859-6',
        'iso-8859-7', 'iso-8859-8', 'iso-8859-9', 'iso-8859-10', 'iso-8859-11', 'iso-8859-12',
        'iso-8859-13', 'iso-8859-14', 'iso-8859-15', 'iso-8859-16',
        'koi8-r', 'koi8-u', 'koi8-ru',
        'cp1125', 'cp1250', 'cp1251', 'cp1252', 'cp1253', 'cp1254', 'cp1257', 'cp949', 'cp1133',
        'cp850', 'cp866', 'cp1255', 'cp1256', 'cp862', 'cp874', 'cp932', 'cp950', 'cp1258',
        'windows-1250', 'windows-1251', 'windows-1252', 'windows-1253', 'windows-1254', 'windows-1255',
        'windows-1256', 'windows-1257', 'windows-1258', 'windows-874',
        'macroman', 'maccentraleurope', 'maciceland', 'maccroatian', 'macromania', 'maccyrillic',
        'macukraine', 'macgreek', 'macturkish', 'macintosh', 'machebrew', 'macarabic',
        'euc-jp', 'shift_jis', 'iso-2022-jp', 'iso-2022-jp-2', 'iso-2022-jp-1',
        'euc-cn', 'gb2312', 'hz', 'gbk', 'gb18030', 'euc-tw', 'big5', 'big5-hkscs',
        'iso-2022-cn', 'iso-2022-cn-ext', 'euc-kr', 'iso-2022-kr', 'johab',
        'armscii-8', 'georgian-academy', 'georgian-ps', 'koi8-t',
        'tis-620', 'macthai', 'mulelao-1',
        'viscii', 'tcvn', 'hp-roman8', 'nextstep',
        'utf-8', 'ucs-2', 'ucs-2be', 'ucs-2le', 'ucs-4', 'ucs-4be', 'ucs-4le',
        'utf-16', 'utf-16be', 'utf-16le', 'utf-32', 'utf-32be', 'utf-32le', 'utf-7',
        'c99', 'java', 'ucs-2-internal', 'ucs-4-internal'
    ];

    /**
     * @var array
     */
    public static $aLocaleMapping = [
        '.65001' => 'utf-8',
        '.20127' => 'iso-8859-1',

        '.1250' => 'windows-1250',
        '.cp1250' => 'windows-1250',
        '.cp-1250' => 'windows-1250',
        '.1251' => 'windows-1251',
        '.cp1251' => 'windows-1251',
        '.cp-1251' => 'windows-1251',
        '.1252' => 'windows-1252',
        '.cp1252' => 'windows-1252',
        '.cp-1252' => 'windows-1252',
        '.1253' => 'windows-1253',
        '.cp1253' => 'windows-1253',
        '.cp-1253' => 'windows-1253',
        '.1254' => 'windows-1254',
        '.cp1254' => 'windows-1254',
        '.cp-1254' => 'windows-1254',
        '.1255' => 'windows-1255',
        '.cp1255' => 'windows-1255',
        '.cp-1255' => 'windows-1255',
        '.1256' => 'windows-1256',
        '.cp1256' => 'windows-1256',
        '.cp-1256' => 'windows-1256',
        '.1257' => 'windows-1257',
        '.cp1257' => 'windows-1257',
        '.cp-1257' => 'windows-1257',
        '.1258' => 'windows-1258',
        '.cp1258' => 'windows-1258',
        '.cp-1258' => 'windows-1258',

        '.28591' => 'iso-8859-1',
        '.28592' => 'iso-8859-2',
        '.28593' => 'iso-8859-3',
        '.28594' => 'iso-8859-4',
        '.28595' => 'iso-8859-5',
        '.28596' => 'iso-8859-6',
        '.28597' => 'iso-8859-7',
        '.28598' => 'iso-8859-8',
        '.28599' => 'iso-8859-9',
        '.28603' => 'iso-8859-13',
        '.28605' => 'iso-8859-15',

        '.1125' => 'cp1125',
        '.20866' => 'koi8-r',
        '.21866' => 'koi8-u',
        '.950' => 'big5',
        '.936' => 'euc-cn',
        '.20932' => 'euc-js',
        '.949' => 'euc-kr',
    ];

    /**
     * @param string $sCharset
     * @param string $sValue
     *
     * @return string
     */
    public static function normalizeCharsetByValue($sCharset, $sValue) {
        $sCharset = \CEmail_Client_Utils::normalizeCharset($sCharset);

        if (\CEmail_Client_Utils::CHARSET_UTF_8 !== $sCharset
            && \CEmail_Client_Utils::isUtf8($sValue)
            && false === \strpos($sCharset, \CEmail_Client_Utils::CHARSET_ISO_2022_JP)
        ) {
            $sCharset = \CEmail_Client_Utils::CHARSET_UTF_8;
        }

        return $sCharset;
    }

    /**
     * @param string $sEncodedValue
     * @param string $sIncomingCharset       = ''
     * @param string $sForcedIncomingCharset = ''
     *
     * @return string
     */
    public static function decodeHeaderValue($sEncodedValue, $sIncomingCharset = '', $sForcedIncomingCharset = '') {
        $sValue = $sEncodedValue;
        if (0 < \strlen($sIncomingCharset)) {
            $sIncomingCharset = \CEmail_Client_Utils::normalizeCharsetByValue($sIncomingCharset, $sValue);

            $sValue = \CEmail_Client_Utils::convertEncoding(
                $sValue,
                $sIncomingCharset,
                \CEmail_Client_Utils::CHARSET_UTF_8
            );
        }

        $sValue = \preg_replace('/\?=[\n\r\t\s]{1,5}=\?/m', '?==?', $sValue);
        $sValue = \preg_replace('/[\r\n\t]+/m', ' ', $sValue);

        $aEncodeArray = [''];
        $aMatch = [];
        \preg_match_all('/=\?[^\?]+\?[q|b|Q|B]\?[^\?]*(\?=)/', $sValue, $aMatch);

        if (isset($aMatch[0]) && \is_array($aMatch[0])) {
            for ($iIndex = 0, $iLen = \count($aMatch[0]); $iIndex < $iLen; $iIndex++) {
                if (isset($aMatch[0][$iIndex])) {
                    $iPos = @\strpos($aMatch[0][$iIndex], '*');
                    if (false !== $iPos) {
                        $aMatch[0][$iIndex][0] = \substr($aMatch[0][$iIndex][0], 0, $iPos);
                    }
                }
            }

            $aEncodeArray = $aMatch[0];
        }

        $aParts = [];

        $sMainCharset = '';
        $bOneCharset = true;

        for ($iIndex = 0, $iLen = \count($aEncodeArray); $iIndex < $iLen; $iIndex++) {
            $aTempArr = ['', $aEncodeArray[$iIndex]];
            if ('=?' === \substr(\trim($aTempArr[1]), 0, 2)) {
                $iPos = \strpos($aTempArr[1], '?', 2);
                $aTempArr[0] = \substr($aTempArr[1], 2, $iPos - 2);
                $sEncType = \strtoupper($aTempArr[1][$iPos + 1]);
                switch ($sEncType) {
                    case 'Q':
                        $sHeaderValuePart = \str_replace('_', ' ', $aTempArr[1]);
                        $aTempArr[1] = \quoted_printable_decode(\substr(
                            $sHeaderValuePart,
                            $iPos + 3,
                            \strlen($sHeaderValuePart) - $iPos - 5
                        ));

                        break;
                    case 'B':
                        $sHeaderValuePart = $aTempArr[1];
                        $aTempArr[1] = \CEmail_Client_Utils::base64Decode(\substr(
                            $sHeaderValuePart,
                            $iPos + 3,
                            \strlen($sHeaderValuePart) - $iPos - 5
                        ));

                        break;
                }
            }

            if (0 < \strlen($aTempArr[0])) {
                $sCharset = 0 === \strlen($sForcedIncomingCharset) ? $aTempArr[0] : $sForcedIncomingCharset;
                $sCharset = \CEmail_Client_Utils::normalizeCharset($sCharset, true);

                if ('' === $sMainCharset) {
                    $sMainCharset = $sCharset;
                } elseif ($sMainCharset !== $sCharset) {
                    $bOneCharset = false;
                }
            }

            $aParts[] = [
                $aEncodeArray[$iIndex],
                $aTempArr[1],
                $sCharset
            ];

            unset($aTempArr);
        }

        for ($iIndex = 0, $iLen = \count($aParts); $iIndex < $iLen; $iIndex++) {
            if ($bOneCharset) {
                $sValue = \str_replace($aParts[$iIndex][0], $aParts[$iIndex][1], $sValue);
            } else {
                $aParts[$iIndex][2] = \CEmail_Client_Utils::normalizeCharsetByValue($aParts[$iIndex][2], $aParts[$iIndex][1]);

                $sValue = \str_replace(
                    $aParts[$iIndex][0],
                    \CEmail_Client_Utils::convertEncoding($aParts[$iIndex][1], $aParts[$iIndex][2], \CEmail_Client_Utils::CHARSET_UTF_8),
                    $sValue
                );
            }
        }

        if ($bOneCharset && 0 < \strlen($sMainCharset)) {
            $sMainCharset = \CEmail_Client_Utils::normalizeCharsetByValue($sMainCharset, $sValue);
            $sValue = \CEmail_Client_Utils::convertEncoding($sValue, $sMainCharset, \CEmail_Client_Utils::CHARSET_UTF_8);
        }

        return $sValue;
    }

    /**
     * @param string $sEncoding
     * @param bool   $bAsciAsUtf8 = false
     *
     * @return string
     */
    public static function normalizeCharset($sEncoding, $bAsciAsUtf8 = false) {
        $sEncoding = \strtolower($sEncoding);
        switch ($sEncoding) {
            case 'asci':
            case 'ascii':
            case 'us-asci':
            case 'us-ascii':
                $sEncoding = $bAsciAsUtf8 ? static::CHARSET_UTF_8
                    : static::CHARSET_ISO_8859_1;

                break;
            case 'unicode-1-1-utf-7':
                $sEncoding = static::CHARSET_UTF_7;

                break;
            case 'utf8':
            case 'utf-8':
                $sEncoding = static::CHARSET_UTF_8;

                break;
            case 'utf7imap':
            case 'utf-7imap':
            case 'utf7-imap':
            case 'utf-7-imap':
                $sEncoding = static::CHARSET_UTF_7_IMAP;

                break;
            case 'ks-c-5601-1987':
            case 'ks_c_5601-1987':
            case 'ks_c_5601_1987':
                $sEncoding = 'euc-kr';

                break;
            case 'x-gbk':
                $sEncoding = 'gb2312';

                break;
            case 'iso-8859-i':
            case 'iso-8859-8-i':
                $sEncoding = static::CHARSET_ISO_8859_8;

                break;
        }

        return $sEncoding;
    }

    /**
     * @param string $sInputString
     * @param string $sInputFromEncoding
     * @param string $sInputToEncoding
     *
     * @return string
     */
    public static function convertEncoding($sInputString, $sInputFromEncoding, $sInputToEncoding) {
        $sResult = $sInputString;

        $sFromEncoding = \CEmail_Client_Utils::normalizeCharset($sInputFromEncoding);
        $sToEncoding = \CEmail_Client_Utils::normalizeCharset($sInputToEncoding);

        if ('' === \trim($sResult) || ($sFromEncoding === $sToEncoding && \CEmail_Client_Utils::CHARSET_UTF_8 !== $sFromEncoding)) {
            return $sResult;
        }

        $bUnknown = false;
        switch (true) {
            default:
                $bUnknown = true;

                break;
            case $sFromEncoding === \CEmail_Client_Utils::CHARSET_ISO_8859_1
            && $sToEncoding === \CEmail_Client_Utils::CHARSET_UTF_8
            && \function_exists('utf8_encode'):
                $sResult = \utf8_encode($sResult);

                break;
            case $sFromEncoding === \CEmail_Client_Utils::CHARSET_UTF_8
            && $sToEncoding === \CEmail_Client_Utils::CHARSET_ISO_8859_1
            && \function_exists('utf8_decode'):
                $sResult = \utf8_decode($sResult);

                break;

            case $sFromEncoding === \CEmail_Client_Utils::CHARSET_UTF_7_IMAP
            && $sToEncoding === \CEmail_Client_Utils::CHARSET_UTF_8:
                $sResult = \CEmail_Client_Utils::utf7ModifiedToUtf8($sResult);
                if (false === $sResult) {
                    $sResult = $sInputString;
                }

                break;

            case $sFromEncoding === \CEmail_Client_Utils::CHARSET_UTF_8
            && $sToEncoding === \CEmail_Client_Utils::CHARSET_UTF_7_IMAP:
                $sResult = \CEmail_Client_Utils::Utf8ToUtf7Modified($sResult);
                if (false === $sResult) {
                    $sResult = $sInputString;
                }

                break;

            case $sFromEncoding === \CEmail_Client_Utils::CHARSET_UTF_7_IMAP:
                $sResult = \CEmail_Client_Utils::convertEncoding(
                    \CEmail_Client_Utils::modifiedToPlainUtf7($sResult),
                    \CEmail_Client_Utils::CHARSET_UTF_7,
                    $sToEncoding
                );

                break;

            case \in_array(\strtolower($sFromEncoding), \CEmail_Client_Utils::$suppostedCharsets):
                if (\CEmail_Client_Utils::isIconvSupported()) {
                    $sResult = \CEmail_Client_Utils::iconvConvertEncoding($sResult, $sFromEncoding, $sToEncoding);
                } elseif (\CEmail_Client_Utils::isMbStringSupported()) {
                    $sResult = \CEmail_Client_Utils::mbConvertEncoding($sResult, $sFromEncoding, $sToEncoding);
                }

                $sResult = (false !== $sResult) ? $sResult : $sInputString;

                break;
        }

        if ($bUnknown && \CEmail_Client_Utils::isMbStringSupported()) {
            $sResult = @\mb_convert_encoding($sResult, $sToEncoding);
        }

        return $sResult;
    }

    /**
     * @return bool
     */
    public static function isIconvIgnoreSupported() {
        static $bCache = null;
        if (null !== $bCache) {
            return $bCache;
        }

        $bCache = false;
        if (static::isIconvSupported()) {
            if (false !== @\iconv('', '//IGNORE', '')) {
                $bCache = true;
            }
        }

        return $bCache;
    }

    /**
     * @return bool
     */
    public static function isIconvTranslitSupported() {
        static $bCache = null;
        if (null !== $bCache) {
            return $bCache;
        }

        $bCache = false;
        if (static::IsIconvSupported()) {
            if (false !== @\iconv('', '//TRANSLIT', '')) {
                $bCache = true;
            }
        }

        return $bCache;
    }

    /**
     * @param string $sInputString
     * @param string $sInputFromEncoding
     * @param string $sInputToEncoding
     *
     * @return string|bool
     */
    public static function iconvConvertEncoding($sInputString, $sInputFromEncoding, $sInputToEncoding) {
        $sIconvOptions = '';
        if (static::isIconvIgnoreSupported()) {
            $sIconvOptions .= '//IGNORE';
        }

        $mResult = @\iconv(\strtoupper($sInputFromEncoding), \strtoupper($sInputToEncoding) . $sIconvOptions, $sInputString);
        if (false === $mResult) {
            if (static::isMbStringSupported()) {
                $mResult = static::mbConvertEncoding($sInputString, $sInputFromEncoding, $sInputToEncoding);
            }
        }

        return $mResult;
    }

    /**
     * @param string $sInputString
     * @param string $sInputFromEncoding
     * @param string $sInputToEncoding
     *
     * @return string|bool
     */
    public static function mbConvertEncoding($sInputString, $sInputFromEncoding, $sInputToEncoding) {
        static $sMbstringSubCh = null;
        if (null === $sMbstringSubCh) {
            $sMbstringSubCh = \mb_substitute_character();
        }

        \mb_substitute_character('none');
        $sResult = @\mb_convert_encoding($sInputString, \strtoupper($sInputToEncoding), \strtoupper($sInputFromEncoding));
        \mb_substitute_character($sMbstringSubCh);

        return $sResult;
    }

    /**
     * @param string $sValue
     *
     * @return bool
     */
    public static function isAscii($sValue) {
        if ('' === \trim($sValue)) {
            return true;
        }

        return !\preg_match('/[^\x09\x10\x13\x0A\x0D\x20-\x7E]/', $sValue);
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    public static function strToLowerIfAscii($sValue) {
        return \CEmail_Client_Utils::isAscii($sValue) ? \strtolower($sValue) : $sValue;
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    public static function strToUpperIfAscii($sValue) {
        return \CEmail_Client_Utils::isAscii($sValue) ? \strtoupper($sValue) : $sValue;
    }

    /**
     * @param string $sValue
     *
     * @return bool
     */
    public static function isUtf8($sValue) {
        return (bool) (\function_exists('mb_check_encoding')
            ? \mb_check_encoding($sValue, 'UTF-8') : \preg_match('//u', $sValue));
    }

    /**
     * @param string $sStr
     * @param bool   $bLowerIfAscii = false
     *
     * @return string
     */
    public static function idnToUtf8($sStr, $bLowerIfAscii = false) {
        if (0 < \strlen($sStr) && \preg_match('/(^|\.)xn--/i', $sStr)) {
            try {
                $sStr = $bLowerIfAscii ? static::strToLowerIfAscii($sStr) : $sStr;

                $sUser = '';
                $sDomain = $sStr;
                if (false !== \strpos($sStr, '@')) {
                    $sUser = static::getAccountNameFromEmail($sStr);
                    $sDomain = static::getDomainFromEmail($sStr);
                }

                if (0 < \strlen($sDomain)) {
                    try {
                        $sDomain = self::idn()->decode($sDomain);
                    } catch (\Exception $oException) {
                    }
                }

                $sStr = ('' === $sUser ? '' : $sUser . '@') . $sDomain;
            } catch (\Exception $oException) {
            }
        }

        return $bLowerIfAscii ? static::strToLowerIfAscii($sStr) : $sStr;
    }

    /**
     * @param string $sStr
     * @param bool   $bLowerIfAscii = false
     *
     * @return string
     */
    public static function idnToAscii($sStr, $bLowerIfAscii = false) {
        $sStr = $bLowerIfAscii ? static::strToLowerIfAscii($sStr) : $sStr;

        $sUser = '';
        $sDomain = $sStr;
        if (false !== \strpos($sStr, '@')) {
            $sUser = static::getAccountNameFromEmail($sStr);
            $sDomain = static::getDomainFromEmail($sStr);
        }

        if (0 < \strlen($sDomain) && \preg_match('/[^\x20-\x7E]/', $sDomain)) {
            try {
                $sDomain = self::idn()->encode($sDomain);
            } catch (\Exception $oException) {
            }
        }

        return ('' === $sUser ? '' : $sUser . '@') . $sDomain;
    }

    /**
     * @return CEmail_Client_Idn
     */
    public static function idn() {
        static $oIdn = null;
        if (null === $oIdn) {
            $oIdn = new CEmail_Client_Idn();
        }

        return $oIdn;
    }

    /**
     * @param string $sString
     *
     * @return string
     */
    public static function base64Decode($sString) {
        $sResultString = \base64_decode($sString, true);
        if (false === $sResultString) {
            $sString = \str_replace([' ', "\r", "\n", "\t"], '', $sString);
            $sString = \preg_replace('/[^a-zA-Z0-9=+\/](.*)$/', '', $sString);

            if (false !== \strpos(\trim(\trim($sString), '='), '=')) {
                $sString = \preg_replace('/=([^=])/', '= $1', $sString);
                $aStrings = \explode(' ', $sString);
                foreach ($aStrings as $iIndex => $sParts) {
                    $aStrings[$iIndex] = \base64_decode($sParts);
                }

                $sResultString = \implode('', $aStrings);
            } else {
                $sResultString = \base64_decode($sString);
            }
        }

        return $sResultString;
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    public static function urlSafeBase64Encode($sValue) {
        return \str_replace(['+', '/', '='], ['-', '_', '.'], \base64_encode($sValue));
    }

    /**
     * @param string $sValue
     *
     * @return string
     */
    public static function urlSafeBase64Decode($sValue) {
        $sData = \str_replace(['-', '_', '.'], ['+', '/', '='], $sValue);
        $sMode = \strlen($sData) % 4;
        if ($sMode) {
            $sData .= \substr('====', $sMode);
        }

        return static::base64Decode($sData);
    }

    /**
     * @return bool
     */
    public static function isMbStringSupported() {
        return CBase_DependencyUtils::functionExistsAndEnabled('mb_convert_encoding');
    }

    /**
     * @return bool
     */
    public static function isIconvSupported() {
        return CBase_DependencyUtils::functionExistsAndEnabled('iconv');
    }

    /**
     * @param string $sUtfModifiedString
     *
     * @return string
     */
    public static function modifiedToPlainUtf7($sUtfModifiedString) {
        $sUtf = '';
        $bBase = false;

        for ($iIndex = 0, $iLen = \strlen($sUtfModifiedString); $iIndex < $iLen; $iIndex++) {
            if ('&' === $sUtfModifiedString[$iIndex]) {
                if (isset($sUtfModifiedString[$iIndex + 1]) && '-' === $sUtfModifiedString[$iIndex + 1]) {
                    $sUtf .= '&';
                    $iIndex++;
                } else {
                    $sUtf .= '+';
                    $bBase = true;
                }
            } elseif ($sUtfModifiedString[$iIndex] == '-' && $bBase) {
                $bBase = false;
            } else {
                if ($bBase && ',' === $sUtfModifiedString[$iIndex]) {
                    $sUtf .= '/';
                } elseif (!$bBase && '+' === $sUtfModifiedString[$iIndex]) {
                    $sUtf .= '+-';
                } else {
                    $sUtf .= $sUtfModifiedString[$iIndex];
                }
            }
        }

        return $sUtf;
    }

    /**
     * @param string $sStr
     *
     * @return string|bool
     */
    public static function utf7ModifiedToUtf8($sStr) {
        $aArray = [-1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1, -1,
            -1, -1, -1, -1, -1, -1, -1, -1, -1, 62, 63, -1, -1, -1, 52, 53, 54, 55, 56, 57, 58, 59, 60, 61, -1, -1, -1, -1, -1, -1, -1, 0, 1, 2, 3, 4, 5, 6, 7, 8, 9,
            10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, -1, -1, -1, -1, -1, -1, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38, 39, 40,
            41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, -1, -1, -1, -1, -1];

        $sResult = '';
        $bError = false;
        $iLen = \strlen($sStr);

        for ($iIndex = 0; $iLen > 0; $iIndex++, $iLen--) {
            $sChar = $sStr[$iIndex];
            if ($sChar == '&') {
                $iIndex++;
                $iLen--;

                $sChar = isset($sStr[$iIndex]) ? $sStr[$iIndex] : null;
                if ($sChar === null) {
                    break;
                }

                if ($iLen && $sChar == '-') {
                    $sResult .= '&';

                    continue;
                }

                $iCh = 0;
                $iK = 10;
                for (; $iLen > 0; $iIndex++, $iLen--) {
                    $sChar = $sStr[$iIndex];

                    $iB = $aArray[\ord($sChar)];
                    if ((\ord($sChar) & 0x80) || $iB == -1) {
                        break;
                    }

                    if ($iK > 0) {
                        $iCh |= $iB << $iK;
                        $iK -= 6;
                    } else {
                        $iCh |= $iB >> (-$iK);
                        if ($iCh < 0x80) {
                            if (0x20 <= $iCh && $iCh < 0x7f) {
                                return $bError;
                            }

                            $sResult .= \chr($iCh);
                        } elseif ($iCh < 0x800) {
                            $sResult .= \chr(0xc0 | ($iCh >> 6));
                            $sResult .= \chr(0x80 | ($iCh & 0x3f));
                        } else {
                            $sResult .= \chr(0xe0 | ($iCh >> 12));
                            $sResult .= \chr(0x80 | (($iCh >> 6) & 0x3f));
                            $sResult .= \chr(0x80 | ($iCh & 0x3f));
                        }

                        $iCh = ($iB << (16 + $iK)) & 0xffff;
                        $iK += 10;
                    }
                }

                if (($iCh || $iK < 6)
                    || (!$iLen || $sChar != '-')
                    || ($iLen > 2 && '&' === $sStr[$iIndex + 1] && '-' !== $sStr[$iIndex + 2])
                ) {
                    return $bError;
                }
            } elseif (\ord($sChar) < 0x20 || \ord($sChar) >= 0x7f) {
                return $bError;
            } else {
                $sResult .= $sChar;
            }
        }

        return $sResult;
    }

    /**
     * @param string $sStr
     *
     * @return string|bool
     */
    public static function utf8ToUtf7Modified($sStr) {
        $sArray = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S',
            'T', 'U', 'V', 'W', 'X', 'Y', 'Z', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o',
            'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '+', ','];

        $sLen = \strlen($sStr);
        $bIsB = false;
        $iIndex = $iN = 0;
        $sReturn = '';
        $bError = false;
        $iCh = $iB = $iK = 0;

        while ($sLen) {
            $iC = \ord($sStr[$iIndex]);
            if ($iC < 0x80) {
                $iCh = $iC;
                $iN = 0;
            } elseif ($iC < 0xc2) {
                return $bError;
            } elseif ($iC < 0xe0) {
                $iCh = $iC & 0x1f;
                $iN = 1;
            } elseif ($iC < 0xf0) {
                $iCh = $iC & 0x0f;
                $iN = 2;
            } elseif ($iC < 0xf8) {
                $iCh = $iC & 0x07;
                $iN = 3;
            } elseif ($iC < 0xfc) {
                $iCh = $iC & 0x03;
                $iN = 4;
            } elseif ($iC < 0xfe) {
                $iCh = $iC & 0x01;
                $iN = 5;
            } else {
                return $bError;
            }

            $iIndex++;
            $sLen--;

            if ($iN > $sLen) {
                return $bError;
            }

            for ($iJ = 0; $iJ < $iN; $iJ++) {
                $iO = \ord($sStr[$iIndex + $iJ]);
                if (($iO & 0xc0) != 0x80) {
                    return $bError;
                }

                $iCh = ($iCh << 6) | ($iO & 0x3f);
            }

            if ($iN > 1 && !($iCh >> ($iN * 5 + 1))) {
                return $bError;
            }

            $iIndex += $iN;
            $sLen -= $iN;

            if ($iCh < 0x20 || $iCh >= 0x7f) {
                if (!$bIsB) {
                    $sReturn .= '&';
                    $bIsB = true;
                    $iB = 0;
                    $iK = 10;
                }

                if ($iCh & ~0xffff) {
                    $iCh = 0xfffe;
                }

                $sReturn .= $sArray[($iB | $iCh >> $iK)];
                $iK -= 6;
                for (; $iK >= 0; $iK -= 6) {
                    $sReturn .= $sArray[(($iCh >> $iK) & 0x3f)];
                }

                $iB = ($iCh << (-$iK)) & 0x3f;
                $iK += 16;
            } else {
                if ($bIsB) {
                    if ($iK > 10) {
                        $sReturn .= $sArray[$iB];
                    }
                    $sReturn .= '-';
                    $bIsB = false;
                }

                $sReturn .= \chr($iCh);
                if ('&' === \chr($iCh)) {
                    $sReturn .= '-';
                }
            }
        }

        if ($bIsB) {
            if ($iK > 10) {
                $sReturn .= $sArray[$iB];
            }

            $sReturn .= '-';
        }

        return $sReturn;
    }

    /**
     * @param string $sEmail
     *
     * @return string
     */
    public static function getAccountNameFromEmail($sEmail) {
        $sResult = '';
        if (0 < \strlen($sEmail)) {
            $iPos = \strpos($sEmail, '@');
            $sResult = (false === $iPos) ? $sEmail : \substr($sEmail, 0, $iPos);
        }

        return $sResult;
    }

    /**
     * @param string $sEmail
     *
     * @return string
     */
    public static function getDomainFromEmail($sEmail) {
        $sResult = '';
        if (0 < \strlen($sEmail)) {
            $iPos = \strpos($sEmail, '@');
            if (false !== $iPos && 0 < $iPos) {
                $sResult = \substr($sEmail, $iPos + 1);
            }
        }

        return $sResult;
    }

    /**
     * @param string $sFileName
     *
     * @return string
     */
    public static function getFileExtension($sFileName) {
        $iLast = \strrpos($sFileName, '.');

        return false === $iLast ? '' : \strtolower(\substr($sFileName, $iLast + 1));
    }

    /**
     * @param string $sText
     * @param string $sChar
     *
     * @return string
     */
    public static function customTrim($sText, $sChar = null) {
        while (strlen($sText) > 1 && ($sChar ? substr($sText, 0, 1) === $sChar : true) && (substr($sText, 0, 1) === substr($sText, -1))) {
            $sText = substr($sText, 1);
            $sText = substr($sText, 0, strlen($sText) - 1);
        }

        return $sText;
    }

    /**
     * @param string $sEncodeType
     * @param string $sValue
     *
     * @return string
     */
    public static function encodeUnencodedValue($sEncodeType, $sValue) {
        $sValue = \trim($sValue);
        if (0 < \strlen($sValue) && !static::IsAscii($sValue)) {
            switch (\strtoupper($sEncodeType)) {
                case 'B':
                    $sValue = '=?' . \strtolower(static::CHARSET_UTF_8)
                        . '?B?' . \base64_encode($sValue) . '?=';

                    break;

                case 'Q':
                    $sValue = '=?' . \strtolower(static::CHARSET_UTF_8)
                        . '?Q?' . \str_replace(
                            ['?', ' ', '_'],
                            ['=3F', '_', '=5F'],
                            \quoted_printable_encode($sValue)
                        ) . '?=';

                    break;
            }
        }

        return $sValue;
    }
}

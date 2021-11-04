<?php

class CUTF8_Native {
    use CUTF8_Trait_LowerUpperTrait;

    public static function ltrim($str, $charlist = null) {
        if ($charlist === null) {
            return ltrim($str);
        }

        if (CUTF8::isAscii($charlist)) {
            return ltrim($str, $charlist);
        }

        $charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

        return preg_replace('/^[' . $charlist . ']+/u', '', $str);
    }

    public static function rtrim($str, $charlist = null) {
        if ($charlist === null) {
            return rtrim($str);
        }

        if (CUTF8::isAscii($charlist)) {
            return rtrim($str, $charlist);
        }

        $charlist = preg_replace('#[-\[\]:\\\\^/]#', '\\\\$0', $charlist);

        return preg_replace('/[' . $charlist . ']++$/uD', '', $str);
    }

    public static function ord($chr) {
        $ord0 = ord($chr);

        if ($ord0 >= 0 and $ord0 <= 127) {
            return $ord0;
        }

        if (!isset($chr[1])) {
            throw new CUTF8_Exception('Short sequence - at least 2 bytes expected, only 1 seen');
        }

        $ord1 = ord($chr[1]);

        if ($ord0 >= 192 and $ord0 <= 223) {
            return ($ord0 - 192) * 64 + ($ord1 - 128);
        }

        if (!isset($chr[2])) {
            throw new CUTF8_Exception('Short sequence - at least 3 bytes expected, only 2 seen');
        }

        $ord2 = ord($chr[2]);

        if ($ord0 >= 224 and $ord0 <= 239) {
            return ($ord0 - 224) * 4096 + ($ord1 - 128) * 64 + ($ord2 - 128);
        }

        if (!isset($chr[3])) {
            throw new CUTF8_Exception('Short sequence - at least 4 bytes expected, only 3 seen');
        }

        $ord3 = ord($chr[3]);

        if ($ord0 >= 240 and $ord0 <= 247) {
            return ($ord0 - 240) * 262144 + ($ord1 - 128) * 4096 + ($ord2 - 128) * 64 + ($ord3 - 128);
        }

        if (!isset($chr[4])) {
            throw new CUTF8_Exception('Short sequence - at least 5 bytes expected, only 4 seen');
        }

        $ord4 = ord($chr[4]);

        if ($ord0 >= 248 and $ord0 <= 251) {
            return ($ord0 - 248) * 16777216 + ($ord1 - 128) * 262144 + ($ord2 - 128) * 4096 + ($ord3 - 128) * 64 + ($ord4 - 128);
        }

        if (!isset($chr[5])) {
            throw new CUTF8_Exception('Short sequence - at least 6 bytes expected, only 5 seen');
        }

        if ($ord0 >= 252 and $ord0 <= 253) {
            return ($ord0 - 252) * 1073741824 + ($ord1 - 128) * 16777216 + ($ord2 - 128) * 262144 + ($ord3 - 128) * 4096 + ($ord4 - 128) * 64 + (ord($chr[5]) - 128);
        }

        if ($ord0 >= 254 and $ord0 <= 255) {
            throw new CUTF8_Exception("Invalid UTF-8 with surrogate ordinal ':ordinal'", [
                ':ordinal' => $ord0,
            ]);
        }
    }

    public static function strlen($str) {
        if (CUTF8::isAscii($str)) {
            return strlen($str);
        }

        return strlen(utf8_decode($str));
    }

    public static function fromUnicode($arr) {
        ob_start();

        $keys = array_keys($arr);

        foreach ($keys as $k) {
            // ASCII range (including control chars)
            if (($arr[$k] >= 0) and ($arr[$k] <= 0x007f)) {
                echo chr($arr[$k]);
            } elseif ($arr[$k] <= 0x07ff) {
                // 2 byte sequence
                echo chr(0xc0 | ($arr[$k] >> 6));
                echo chr(0x80 | ($arr[$k] & 0x003f));
            } elseif ($arr[$k] == 0xFEFF) {
                // Byte order mark (skip)

                // nop -- zap the BOM
            } elseif ($arr[$k] >= 0xD800 and $arr[$k] <= 0xDFFF) {
                // Test for illegal surrogates
                // Found a surrogate
                throw new CUTF8_Exception("CUTF8::from_unicode: Illegal surrogate at index: ':index', value: ':value'", [
                    ':index' => $k,
                    ':value' => $arr[$k],
                ]);
            } elseif ($arr[$k] <= 0xffff) {
                // 3 byte sequence
                echo chr(0xe0 | ($arr[$k] >> 12));
                echo chr(0x80 | (($arr[$k] >> 6) & 0x003f));
                echo chr(0x80 | ($arr[$k] & 0x003f));
            } elseif ($arr[$k] <= 0x10ffff) {
                // 4 byte sequence
                echo chr(0xf0 | ($arr[$k] >> 18));
                echo chr(0x80 | (($arr[$k] >> 12) & 0x3f));
                echo chr(0x80 | (($arr[$k] >> 6) & 0x3f));
                echo chr(0x80 | ($arr[$k] & 0x3f));
            } else {
                // Out of range
                throw new CUTF8_Exception("CUTF8::from_unicode: Codepoint out of Unicode range at index: ':index', value: ':value'", [
                    ':index' => $k,
                    ':value' => $arr[$k],
                ]);
            }
        }

        $result = ob_get_contents();
        ob_end_clean();

        return $result;
    }

    public static function strtolower($str) {
        if (CUTF8::isAscii($str)) {
            return strtolower($str);
        }
        $upperToLower = static::getUpperToLower();

        $uni = CUTF8::toUnicode($str);

        if ($uni === false) {
            return false;
        }

        for ($i = 0, $c = count($uni); $i < $c; $i++) {
            if (isset($upperToLower[$uni[$i]])) {
                $uni[$i] = $upperToLower[$uni[$i]];
            }
        }

        return CUTF8::fromUnicode($uni);
    }

    public static function toUnicode($str) {
        // Cached expected number of octets after the current octet until the beginning of the next CUTF8 character sequence
        $m_state = 0;
        // Cached Unicode character
        $m_ucs4 = 0;
        // Cached expected number of octets in the current sequence
        $m_bytes = 1;

        $out = [];

        $len = strlen($str);

        for ($i = 0; $i < $len; $i++) {
            $in = ord($str[$i]);

            if ($m_state == 0) {
                // When m_state is zero we expect either a US-ASCII character or a multi-octet sequence.
                if (0 == (0x80 & $in)) {
                    // US-ASCII, pass straight through.
                    $out[] = $in;
                    $m_bytes = 1;
                } elseif (0xC0 == (0xE0 & $in)) {
                    // First octet of 2 octet sequence
                    $m_ucs4 = $in;
                    $m_ucs4 = ($m_ucs4 & 0x1F) << 6;
                    $m_state = 1;
                    $m_bytes = 2;
                } elseif (0xE0 == (0xF0 & $in)) {
                    // First octet of 3 octet sequence
                    $m_ucs4 = $in;
                    $m_ucs4 = ($m_ucs4 & 0x0F) << 12;
                    $m_state = 2;
                    $m_bytes = 3;
                } elseif (0xF0 == (0xF8 & $in)) {
                    // First octet of 4 octet sequence
                    $m_ucs4 = $in;
                    $m_ucs4 = ($m_ucs4 & 0x07) << 18;
                    $m_state = 3;
                    $m_bytes = 4;
                } elseif (0xF8 == (0xFC & $in)) {
                    /**
                     * First octet of 5 octet sequence.
                     * This is illegal because the encoded codepoint must be either
                     * (a) not the shortest form or
                     * (b) outside the Unicode range of 0-0x10FFFF.
                     * Rather than trying to resynchronize, we will carry on until the end
                     * of the sequence and let the later error handling code catch it.
                     */
                    $m_ucs4 = $in;
                    $m_ucs4 = ($m_ucs4 & 0x03) << 24;
                    $m_state = 4;
                    $m_bytes = 5;
                } elseif (0xFC == (0xFE & $in)) {
                    // First octet of 6 octet sequence, see comments for 5 octet sequence.
                    $m_ucs4 = $in;
                    $m_ucs4 = ($m_ucs4 & 1) << 30;
                    $m_state = 5;
                    $m_bytes = 6;
                } else {
                    // Current octet is neither in the US-ASCII range nor a legal first octet of a multi-octet sequence.
                    trigger_error('CUTF8::to_unicode: Illegal sequence identifier in UTF-8 at byte ' . $i, E_USER_WARNING);

                    return false;
                }
            } else {
                // When m_state is non-zero, we expect a continuation of the multi-octet sequence
                if (0x80 == (0xC0 & $in)) {
                    // Legal continuation
                    $shift = ($m_state - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $m_ucs4 |= $tmp;

                    // End of the multi-octet sequence. mUcs4 now contains the final Unicode codepoint to be output
                    if (0 == --$m_state) {
                        // Check for illegal sequences and codepoints

                        // From Unicode 3.1, non-shortest form is illegal
                        if (((2 == $m_bytes) and ($m_ucs4 < 0x0080))
                            or ((3 == $m_bytes) and ($m_ucs4 < 0x0800))
                            or ((4 == $m_bytes) and ($m_ucs4 < 0x10000))
                            or (4 < $m_bytes)
                            // From Unicode 3.2, surrogate characters are illegal
                            or (($m_ucs4 & 0xFFFFF800) == 0xD800)
                            // Codepoints outside the Unicode range are illegal
                            or ($m_ucs4 > 0x10FFFF)
                        ) {
                            trigger_error('CUTF8::to_unicode: Illegal sequence or codepoint in UTF-8 at byte ' . $i, E_USER_WARNING);

                            return false;
                        }

                        if (0xFEFF != $m_ucs4) {
                            // BOM is legal but we don't want to output it
                            $out[] = $m_ucs4;
                        }

                        // Initialize UTF-8 cache
                        $m_state = 0;
                        $m_ucs4 = 0;
                        $m_bytes = 1;
                    }
                } else {
                    // ((0xC0 & (*in) != 0x80) AND (m_state != 0))
                    // Incomplete multi-octet sequence
                    throw new CUTF8_Exception("CUTF8::to_unicode: Incomplete multi-octet sequence in UTF-8 at byte ':byte'", [
                        ':byte' => $i,
                    ]);
                }
            }
        }

        return $out;
    }

    /**
     * @param string   $str
     * @param int      $offset
     * @param null|int $length
     */
    public static function substr($str, $offset, $length = null) {
        if (CUTF8::isAscii($str)) {
            return ($length === null) ? substr($str, $offset) : substr($str, $offset, $length);
        }

        // Normalize params
        $str = (string) $str;
        $strlen = CUTF8::strlen($str);
        $offset = (int) ($offset < 0) ? max(0, $strlen + $offset) : $offset; // Normalize to positive offset
        $length = ($length === null) ? null : (int) $length;

        // Impossible
        if ($length === 0 or $offset >= $strlen or ($length < 0 and $length <= $offset - $strlen)) {
            return '';
        }

        // Whole string
        if ($offset == 0 and ($length === null or $length >= $strlen)) {
            return $str;
        }

        // Build regex
        $regex = '^';

        // Create an offset expression
        if ($offset > 0) {
            // PCRE repeating quantifiers must be less than 65536, so repeat when necessary
            $x = (int) ($offset / 65535);
            $y = (int) ($offset % 65535);
            $regex .= ($x == 0) ? '' : ('(?:.{65535}){' . $x . '}');
            $regex .= ($y == 0) ? '' : ('.{' . $y . '}');
        }

        // Create a length expression
        if ($length === null) {
            $regex .= '(.*)'; // No length set, grab it all
        } elseif ($length > 0) {
            // Find length from the left (positive length)
            // Reduce length so that it can't go beyond the end of the string
            $length = min($strlen - $offset, $length);

            $x = (int) ($length / 65535);
            $y = (int) ($length % 65535);
            $regex .= '(';
            $regex .= ($x == 0) ? '' : ('(?:.{65535}){' . $x . '}');
            $regex .= '.{' . $y . '})';
        } else {
            // Find length from the right (negative length)
            $x = (int) (-$length / 65535);
            $y = (int) (-$length % 65535);
            $regex .= '(.*)';
            $regex .= ($x == 0) ? '' : ('(?:.{65535}){' . $x . '}');
            $regex .= '.{' . $y . '}';
        }

        preg_match('/' . $regex . '/us', $str, $matches);

        return $matches[1];
    }

    public static function strIreplace($search, $replace, $str, &$count = null) {
        if (CUTF8::isAscii($search) and CUTF8::isAscii($replace) and CUTF8::isAscii($str)) {
            return str_ireplace($search, $replace, $str, $count);
        }

        if (is_array($str)) {
            foreach ($str as $key => $val) {
                $str[$key] = CUTF8::strIreplace($search, $replace, $val, $count);
            }

            return $str;
        }

        if (is_array($search)) {
            $keys = array_keys($search);

            foreach ($keys as $k) {
                if (is_array($replace)) {
                    if (array_key_exists($k, $replace)) {
                        $str = CUTF8::strIreplace($search[$k], $replace[$k], $str, $count);
                    } else {
                        $str = CUTF8::strIreplace($search[$k], '', $str, $count);
                    }
                } else {
                    $str = CUTF8::strIreplace($search[$k], $replace, $str, $count);
                }
            }

            return $str;
        }

        $search = CUTF8::strtolower($search);
        $str_lower = CUTF8::strtolower($str);

        $total_matched_strlen = 0;
        $i = 0;

        while (preg_match('/(.*?)' . preg_quote($search, '/') . '/s', $str_lower, $matches)) {
            $matched_strlen = strlen($matches[0]);
            $str_lower = substr($str_lower, $matched_strlen);

            $offset = $total_matched_strlen + strlen($matches[1]) + ($i * (strlen($replace) - 1));
            $str = substr_replace($str, $replace, $offset, strlen($search));

            $total_matched_strlen += $matched_strlen;
            $i++;
        }

        $count += $i;

        return $str;
    }

    public static function strPad($str, $final_str_length, $pad_str = ' ', $pad_type = STR_PAD_RIGHT) {
        if (CUTF8::isAscii($str) and CUTF8::isAscii($pad_str)) {
            return str_pad($str, $final_str_length, $pad_str, $pad_type);
        }

        $str_length = CUTF8::strlen($str);

        if ($final_str_length <= 0 or $final_str_length <= $str_length) {
            return $str;
        }

        $pad_str_length = CUTF8::strlen($pad_str);
        $pad_length = $final_str_length - $str_length;

        if ($pad_type == STR_PAD_RIGHT) {
            $repeat = ceil($pad_length / $pad_str_length);

            return CUTF8::substr($str . str_repeat($pad_str, $repeat), 0, $final_str_length);
        }

        if ($pad_type == STR_PAD_LEFT) {
            $repeat = ceil($pad_length / $pad_str_length);

            return CUTF8::substr(str_repeat($pad_str, $repeat), 0, floor($pad_length)) . $str;
        }

        if ($pad_type == STR_PAD_BOTH) {
            $pad_length /= 2;
            $pad_length_left = floor($pad_length);
            $pad_length_right = ceil($pad_length);
            $repeat_left = ceil($pad_length_left / $pad_str_length);
            $repeat_right = ceil($pad_length_right / $pad_str_length);

            $pad_left = CUTF8::substr(str_repeat($pad_str, $repeat_left), 0, $pad_length_left);
            $pad_right = CUTF8::substr(str_repeat($pad_str, $repeat_right), 0, $pad_length_right);

            return $pad_left . $str . $pad_right;
        }

        throw new CUTF8_Exception('CUTF8::str_pad: Unknown padding type (:pad_type)', [
            ':pad_type' => $pad_type,
        ]);
    }

    public static function strSplit($str, $split_length = 1) {
        $split_length = (int) $split_length;

        if (CUTF8::isAscii($str)) {
            return str_split($str, $split_length);
        }

        if ($split_length < 1) {
            return false;
        }

        if (CUTF8::strlen($str) <= $split_length) {
            return [$str];
        }

        preg_match_all('/.{' . $split_length . '}|[^\x00]{1,' . $split_length . '}$/us', $str, $matches);

        return $matches[0];
    }

    public static function strcasecmp($str1, $str2) {
        if (CUTF8::isAscii($str1) and CUTF8::isAscii($str2)) {
            return strcasecmp($str1, $str2);
        }

        $str1 = CUTF8::strtolower($str1);
        $str2 = CUTF8::strtolower($str2);

        return strcmp($str1, $str2);
    }

    public static function strcspn($str, $mask, $offset = null, $length = null) {
        if ($str == '' or $mask == '') {
            return 0;
        }

        if (CUTF8::isAscii($str) and CUTF8::isAscii($mask)) {
            return ($offset === null) ? strcspn($str, $mask) : (($length === null) ? strcspn($str, $mask, $offset) : strcspn($str, $mask, $offset, $length));
        }

        if ($offset !== null or $length !== null) {
            $str = CUTF8::substr($str, $offset, $length);
        }

        // Escape these characters:  - [ ] . : \ ^ /
        // The . and : are escaped to prevent possible warnings about POSIX regex elements
        $mask = preg_replace('#[-[\].:\\\\^/]#', '\\\\$0', $mask);
        preg_match('/^[^' . $mask . ']+/u', $str, $matches);

        return isset($matches[0]) ? CUTF8::strlen($matches[0]) : 0;
    }

    public static function stristr($str, $search) {
        if (CUTF8::isAscii($str) and CUTF8::isAscii($search)) {
            return stristr($str, $search);
        }

        if ($search == '') {
            return $str;
        }

        $str_lower = CUTF8::strtolower($str);
        $search_lower = CUTF8::strtolower($search);

        preg_match('/^(.*?)' . preg_quote($search_lower, '/') . '/s', $str_lower, $matches);

        if (isset($matches[1])) {
            return substr($str, strlen($matches[1]));
        }

        return false;
    }

    public static function strspn($str, $mask, $offset = null, $length = null) {
        if ($str == '' or $mask == '') {
            return 0;
        }

        if (CUTF8::isAscii($str) and CUTF8::isAscii($mask)) {
            return ($offset === null) ? strspn($str, $mask) : (($length === null) ? strspn($str, $mask, $offset) : strspn($str, $mask, $offset, $length));
        }

        if ($offset !== null or $length !== null) {
            $str = CUTF8::substr($str, $offset, $length);
        }

        // Escape these characters:  - [ ] . : \ ^ /
        // The . and : are escaped to prevent possible warnings about POSIX regex elements
        $mask = preg_replace('#[-[\].:\\\\^/]#', '\\\\$0', $mask);
        preg_match('/^[^' . $mask . ']+/u', $str, $matches);

        return isset($matches[0]) ? CUTF8::strlen($matches[0]) : 0;
    }

    public static function strpos($str, $search, $offset = 0) {
        $offset = (int) $offset;

        if (CUTF8::isAscii($str) and CUTF8::isAscii($search)) {
            return strpos($str, $search, $offset);
        }

        if ($offset == 0) {
            $array = explode($search, $str, 2);

            return isset($array[1]) ? CUTF8::strlen($array[0]) : false;
        }

        $str = CUTF8::substr($str, $offset);
        $pos = CUTF8::strpos($str, $search);

        return ($pos === false) ? false : ($pos + $offset);
    }

    public static function strrev($str) {
        if (CUTF8::isAscii($str)) {
            return strrev($str);
        }

        preg_match_all('/./us', $str, $matches);

        return implode('', array_reverse($matches[0]));
    }

    public static function strrpos($str, $search, $offset = 0) {
        $offset = (int) $offset;

        if (CUTF8::isAscii($str) and CUTF8::isAscii($search)) {
            return strrpos($str, $search, $offset);
        }

        if ($offset == 0) {
            $array = explode($search, $str, -1);

            return isset($array[0]) ? CUTF8::strlen(implode($search, $array)) : false;
        }

        $str = CUTF8::substr($str, $offset);
        $pos = CUTF8::strrpos($str, $search);

        return ($pos === false) ? false : ($pos + $offset);
    }

    public static function strtoupper($str) {
        if (CUTF8::isAscii($str)) {
            return strtoupper($str);
        }

        $lowerToUpper = static::getLowerToUpper();

        $uni = CUTF8::toUnicode($str);

        if ($uni === false) {
            return false;
        }

        for ($i = 0, $c = count($uni); $i < $c; $i++) {
            if (isset($lowerToUpper[$uni[$i]])) {
                $uni[$i] = $lowerToUpper[$uni[$i]];
            }
        }

        return CUTF8::fromUnicode($uni);
    }

    public static function ucfirst($str) {
        if (CUTF8::isAscii($str)) {
            return ucfirst($str);
        }

        preg_match('/^(.?)(.*)$/us', $str, $matches);

        return CUTF8::strtoupper($matches[1]) . $matches[2];
    }

    public static function ucwords($str) {
        if (CUTF8::isAscii($str)) {
            return ucwords($str);
        }

        // [\x0c\x09\x0b\x0a\x0d\x20] matches form feeds, horizontal tabs, vertical tabs, linefeeds and carriage returns.
        // This corresponds to the definition of a 'word' defined at http://php.net/ucwords
        return preg_replace_callback(
            '/(?<=^|[\x0c\x09\x0b\x0a\x0d\x20])[^\x0c\x09\x0b\x0a\x0d\x20]/u',
            function ($matches) {
                return CUTF8::strtoupper($matches[0]);
            },
            $str
        );
    }

    public static function substrReplace($str, $replacement, $offset, $length = null) {
        if (CUTF8::isAscii($str)) {
            return ($length === null) ? substr_replace($str, $replacement, $offset) : substr_replace($str, $replacement, $offset, $length);
        }

        $length = ($length === null) ? CUTF8::strlen($str) : (int) $length;
        preg_match_all('/./us', $str, $str_array);
        preg_match_all('/./us', $replacement, $replacement_array);

        array_splice($str_array[0], $offset, $length, $replacement_array[0]);

        return implode('', $str_array[0]);
    }

    public static function transliterateToAscii($str, $case = 0) {
        if ($case <= 0) {
            $lowerAccents = static::getLowerAccents();

            $str = str_replace(
                array_keys($lowerAccents),
                array_values($lowerAccents),
                $str
            );
        }

        if ($case >= 0) {
            $upperAccents = static::getUpperAccents();

            $str = str_replace(
                array_keys($upperAccents),
                array_values($upperAccents),
                $str
            );
        }

        return $str;
    }

    public static function trim($str, $charlist = null) {
        if ($charlist === null) {
            return trim($str);
        }

        return CUTF8::ltrim(CUTF8::rtrim($str, $charlist), $charlist);
    }
}

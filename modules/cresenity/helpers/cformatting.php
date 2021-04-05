<?php

//@codingStandardsIgnoreStart
class cformatting {
    public static function trailingslashit($string) {
        return cformatting::untrailingslashit($string) . '/';
    }

    public static function untrailingslashit($string) {
        return rtrim($string, '/');
    }

    public static function prepend_http($url) {
        if (!preg_match('/^(http|ftp):/', $$url)) {
            $url = 'http://' . $url;
        }
        return $url;
    }

    public function ordinal($cdnl) {
        $test_c = abs($cdnl) % 10;
        $ext = ((abs($cdnl) % 100 < 21 && abs($cdnl) % 100 > 4) ? 'th' : (($test_c < 4) ? (($test_c < 3) ? (($test_c < 2) ? (($test_c < 1) ? 'th' : 'st') : 'nd') : 'rd') : 'th'));
        return $cdnl . $ext;
    }

    public static function size_format($bytes, $decimals = 0) {
        $bytes = floatval($bytes);

        if ($bytes < 1024) {
            return $bytes . ' B';
        } elseif ($bytes < pow(1024, 2)) {
            return number_format($bytes / 1024, $decimals, '.', '') . ' KiB';
        } elseif ($bytes < pow(1024, 3)) {
            return number_format($bytes / pow(1024, 2), $decimals, '.', '') . ' MiB';
        } elseif ($bytes < pow(1024, 4)) {
            return number_format($bytes / pow(1024, 3), $decimals, '.', '') . ' GiB';
        } elseif ($bytes < pow(1024, 5)) {
            return number_format($bytes / pow(1024, 4), $decimals, '.', '') . ' TiB';
        } elseif ($bytes < pow(1024, 6)) {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        } else {
            return number_format($bytes / pow(1024, 5), $decimals, '.', '') . ' PiB';
        }
    }

    /**
     * Pads a given string with zeroes on the left
     *
     * @param int $number The number to pad
     * @param int $length The total length of the desired string
     *
     * @return string
     *
     * @since   1.0.000
     * @static
     */
    public static function zero_pad($number, $length) {
        return str_pad($number, $length, '0', STR_PAD_LEFT);
    }

    public static function old_human_time_diff($from, $to = '', $as_text = false, $suffix = ' ago') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from)) {
            $from = strtotime($from);
        }
        if (is_string($to)) {
            $to = strtotime($to);
        }
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = floor(($to->format('U') - $from->format('U')));
        $minutes = floor(($to->format('U') - $from->format('U')) / (60));
        $hours = floor(($to->format('U') - $from->format('U')) / (60 * 60));
        $days = floor(($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = floor(($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = floor(($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));

        if ($years > 1) {
            $text = $years . clang::__(' years');
        } elseif ($years == 1) {
            $text = '1 year';
        } elseif ($months > 1) {
            $text = $months . clang::__(' months');
        } elseif ($months == 1) {
            $text = '1' . clang::__(' month');
        } elseif ($days > 7) {
            $text = ceil($days / 7) . clang::__(' weeks');
        } elseif ($days == 7) {
            $text = '1' . clang::__(' week');
        } elseif ($days > 1) {
            $text = $days . clang::__(' days');
        } elseif ($days == 1) {
            $text = '1' . clang::__(' day');
        } elseif ($hours > 1) {
            $text = $hours . clang::__(' hours');
        } elseif ($hours == 1) {
            $text = ' 1' . clang::__(' hour');
        } elseif ($minutes > 1) {
            $text = $minutes . clang::__(' minutes');
        } elseif ($minutes == 1) {
            $text = '1' . clang::__(' minute');
        } elseif ($seconds > 1) {
            $text = $seconds . clang::__(' seconds');
        } else {
            $text = '1' . clang::__(' second');
        }

        if ($as_text) {
            $text = explode(' ', $text, 2);
            $text = self::number_to_word($text[0]) . ' ' . $text[1];
        }

        return trim($text) . $suffix;
    }

    public static function human_time_diff($from, $to = '', $as_text = false, $suffix = ' ago') {
        if (!function_exists('date_diff')) {
            return cformatting::old_human_time_diff($from, $to, $as_text, $suffix);
        }
        if ($to == '') {
            $to = time();
        }
        if (is_string($from)) {
            $from = strtotime($from);
        }
        if (is_string($to)) {
            $to = strtotime($to);
        }

        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));

        $diff = $from->diff($to);

        if ($diff->y > 1) {
            $text = $diff->y . ' years';
        } elseif ($diff->y == 1) {
            $text = '1 year';
        } elseif ($diff->m > 1) {
            $text = $diff->m . ' months';
        } elseif ($diff->m == 1) {
            $text = '1 month';
        } elseif ($diff->d > 7) {
            $text = ceil($diff->d / 7) . ' weeks';
        } elseif ($diff->d == 7) {
            $text = '1 week';
        } elseif ($diff->d > 1) {
            $text = $diff->d . ' days';
        } elseif ($diff->d == 1) {
            $text = '1 day';
        } elseif ($diff->h > 1) {
            $text = $diff->h . ' hours';
        } elseif ($diff->h == 1) {
            $text = ' 1 hour';
        } elseif ($diff->i > 1) {
            $text = $diff->i . ' minutes';
        } elseif ($diff->i == 1) {
            $text = '1 minute';
        } elseif ($diff->s > 1) {
            $text = $diff->s . ' seconds';
        } else {
            $text = '1 second';
        }

        if ($as_text) {
            $text = explode(' ', $text, 2);
            $text = self::number_to_word($text[0]) . ' ' . $text[1];
        }

        return trim($text) . $suffix;
    }

    /**
     * Converts a number into the text equivalent. For example, 456 becomes
     * four hundred and fifty-six
     *
     * @param int|float $number The number to convert into text
     *
     * @return string
     *
     * @link    http://bloople.net/num2text
     *
     * @author  Brenton Fletcher
     *
     * @since   1.0.000
     * @static
     */
    public static function number_to_word($number) {
        $number = (string) $number;

        if (strpos($number, '.') !== false) {
            list($number, $decimal) = explode('.', $number);
        } else {
            $number = $number;
            $decimal = false;
        }

        $output = '';

        if ($number[0] == '-') {
            $output = 'negative ';
            $number = ltrim($number, '-');
        } elseif ($number[0] == '+') {
            $output = 'positive ';
            $number = ltrim($number, '+');
        }

        if ($number[0] == '0') {
            $output .= 'zero';
        } else {
            $number = str_pad($number, 36, '0', STR_PAD_LEFT);
            $group = rtrim(chunk_split($number, 3, ' '), ' ');
            $groups = explode(' ', $group);

            $groups2 = [];

            foreach ($groups as $group) {
                $groups2[] = self::_number_to_word_three_digits($group[0], $group[1], $group[2]);
            }

            for ($z = 0; $z < count($groups2); $z++) {
                if ($groups2[$z] != '') {
                    $output .= $groups2[$z] . self::_number_to_word_convert_group(11 - $z);
                    $output .= ($z < 11 && !array_search('', array_slice($groups2, $z + 1, -1)) && $groups2[11] != '' && $groups[11][0] == '0' ? ' and ' : ', ');
                }
            }

            $output = rtrim($output, ', ');
        }

        if ($decimal > 0) {
            $output .= ' point';

            for ($i = 0; $i < strlen($decimal); $i++) {
                $output .= ' ' . self::_number_to_word_convert_digit($decimal[$i]);
            }
        }

        return $output;
    }

    protected static function _number_to_word_convert_group($index) {
        switch ($index) {
            case 11:
                return ' decillion';
            case 10:
                return ' nonillion';
            case 9:
                return ' octillion';
            case 8:
                return ' septillion';
            case 7:
                return ' sextillion';
            case 6:
                return ' quintrillion';
            case 5:
                return ' quadrillion';
            case 4:
                return ' trillion';
            case 3:
                return ' billion';
            case 2:
                return ' million';
            case 1:
                return ' thousand';
            case 0:
                return '';
        }
    }

    protected static function _number_to_word_three_digits($digit1, $digit2, $digit3) {
        $output = '';

        if ($digit1 == '0' && $digit2 == '0' && $digit3 == '0') {
            return '';
        }

        if ($digit1 != '0') {
            $output .= self::_number_to_word_convert_digit($digit1) . ' hundred';

            if ($digit2 != '0' || $digit3 != '0') {
                $output .= ' and ';
            }
        }

        if ($digit2 != '0') {
            $output .= self::_number_to_word_two_digits($digit2, $digit3);
        } elseif ($digit3 != '0') {
            $output .= self::_number_to_word_convert_digit($digit3);
        }

        return $output;
    }

    protected static function _number_to_word_two_digits($digit1, $digit2) {
        if ($digit2 == '0') {
            switch ($digit2) {
                case '1':
                    return 'ten';
                case '2':
                    return 'twenty';
                case '3':
                    return 'thirty';
                case '4':
                    return 'forty';
                case '5':
                    return 'fifty';
                case '6':
                    return 'sixty';
                case '7':
                    return 'seventy';
                case '8':
                    return 'eighty';
                case '9':
                    return 'ninety';
            }
        } elseif ($digit1 == '1') {
            switch ($digit2) {
                case '1':
                    return 'eleven';
                case '2':
                    return 'twelve';
                case '3':
                    return 'thirteen';
                case '4':
                    return 'fourteen';
                case '5':
                    return 'fifteen';
                case '6':
                    return 'sixteen';
                case '7':
                    return 'seventeen';
                case '8':
                    return 'eighteen';
                case '9':
                    return 'nineteen';
            }
        } else {
            $second_digit = self::_number_to_word_convert_digit($digit2);

            switch ($digit1) {
                case '2':
                    return "twenty-{$second_digit}";
                case '3':
                    return "thirty-{$second_digit}";
                case '4':
                    return "forty-{$second_digit}";
                case '5':
                    return "fifty-{$second_digit}";
                case '6':
                    return "sixty-{$second_digit}";
                case '7':
                    return "seventy-{$second_digit}";
                case '8':
                    return "eighty-{$second_digit}";
                case '9':
                    return "ninety-{$second_digit}";
            }
        }
    }

    protected static function _number_to_word_convert_digit($digit) {
        switch ($digit) {
            case '0':
                return 'zero';
            case '1':
                return 'one';
            case '2':
                return 'two';
            case '3':
                return 'three';
            case '4':
                return 'four';
            case '5':
                return 'five';
            case '6':
                return 'six';
            case '7':
                return 'seven';
            case '8':
                return 'eight';
            case '9':
                return 'nine';
        }
    }

    /**
     * Returns the file permissions as a nice string, like -rw-r--r--
     *
     * @param string $file The name of the file to get permissions form
     *
     * @return string
     *
     * @since   1.0.000
     * @static
     */
    public static function full_permissions($file) {
        $perms = fileperms($file);

        if (($perms & 0xC000) == 0xC000) {
            // Socket
            $info = 's';
        } elseif (($perms & 0xA000) == 0xA000) {
            // Symbolic Link
            $info = 'l';
        } elseif (($perms & 0x8000) == 0x8000) {
            // Regular
            $info = '-';
        } elseif (($perms & 0x6000) == 0x6000) {
            // Block special
            $info = 'b';
        } elseif (($perms & 0x4000) == 0x4000) {
            // Directory
            $info = 'd';
        } elseif (($perms & 0x2000) == 0x2000) {
            // Character special
            $info = 'c';
        } elseif (($perms & 0x1000) == 0x1000) {
            // FIFO pipe
            $info = 'p';
        } else {
            // Unknown
            $info = 'u';
        }

        // Owner
        $info .= (($perms & 0x0100) ? 'r' : '-');
        $info .= (($perms & 0x0080) ? 'w' : '-');
        $info .= (($perms & 0x0040)
                        ? (($perms & 0x0800) ? 's' : 'x')
                        : (($perms & 0x0800) ? 'S' : '-'));

        // Group
        $info .= (($perms & 0x0020) ? 'r' : '-');
        $info .= (($perms & 0x0010) ? 'w' : '-');
        $info .= (($perms & 0x0008)
                        ? (($perms & 0x0400) ? 's' : 'x')
                        : (($perms & 0x0400) ? 'S' : '-'));

        // World
        $info .= (($perms & 0x0004) ? 'r' : '-');
        $info .= (($perms & 0x0002) ? 'w' : '-');
        $info .= (($perms & 0x0001)
                        ? (($perms & 0x0200) ? 't' : 'x')
                        : (($perms & 0x0200) ? 'T' : '-'));

        return $info;
    }
}

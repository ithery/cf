<?php

class cstr {

    public static function strip_thousand_separator($val) {
        $x = $val;
        $dec_sep = "";
        if (strlen($x) > 3) {
            $dec_sep = substr($x, strlen($x) - 3, 1);
        }
        if ($dec_sep == "," || $dec_sep == ".") {
            if ($dec_sep == ",") {
                $x = str_replace(".", "", $x);
            } else {
                $x = str_replace(",", "", $x);
            }
        } else {
            $x = str_replace(".", "", $x);
            $x = str_replace(",", "", $x);
        }
        return $x;
    }

    public static function replace_id_month($val) {
        $val_new = $val;
        $val_new = str_replace("Januari", "January", $val_new);
        $val_new = str_replace("Februari", "February", $val_new);
        $val_new = str_replace("Maret", "March", $val_new);
        $val_new = str_replace("April", "April", $val_new);
        $val_new = str_replace("Mei", "May", $val_new);
        $val_new = str_replace("Juni", "June", $val_new);
        $val_new = str_replace("Juli", "July", $val_new);
        $val_new = str_replace("Agustus", "August", $val_new);
        $val_new = str_replace("September", "September", $val_new);
        $val_new = str_replace("Oktober", "October", $val_new);
        $val_new = str_replace("November", "November", $val_new);
        $val_new = str_replace("Desember", "December", $val_new);

        $val_new = str_replace("Agust", "Aug", $val_new);
        $val_new = str_replace("Agu", "Aug", $val_new);
        $val_new = str_replace("Okt", "Oct", $val_new);
        $val_new = str_replace("Des", "Dec", $val_new);
        $val_new = str_replace("Nop", "Nov", $val_new);

        return $val_new;
    }

    public static function len($str) {
        return strlen($str);
    }

    public static function toupper($str) {
        return strtoupper($str);
    }

    public static function tolower($str) {
        return strtolower($str);
    }

    public static function pos($string, $needle, $offset = 0) {
        return strpos($string, $needle, $offset);
    }

    public static function between($open, $close, $str) {
        $start_index = strpos($str, $open);
        $end_index = strpos($str, $close);
        $start_index += cstr::len($open);
        $str = substr($str, $start_index, $end_index - $start_index);
        return $str;
    }

    public static function sanitize($string = '', $is_filename = FALSE) {
        // Replace all weird characters with dashes
        $string = preg_replace('/[^\w\-' . ($is_filename ? '~_\.' : '') . ']+/u', '-', $string);

        // Only allow one dash separator at a time (and make string lowercase)
        return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
    }

    public static function ellipsis($str, $length) {
        if ((strlen($str) + 3) > $length)
            $str = substr($str, 0, $length) . "...";
        return $str;
    }

    public static function between_replace($open, $close, &$in, $with, $limit = false, $from = 0) {
        if ($limit !== false && $limit == 0) {
            return $in;
        }
        $open_position = strpos($in, $open, $from);
        if ($open_position === false) {
            return false;
        };
        $close_position = strpos($in, $close, $open_position + strlen($open));
        if ($close_position === false) {
            return false;
        };
        $current = false;
        if (strpos($with, '{*}') !== false) {
            $current = substr($in, $open_position + strlen($open), $close_position - $open_position - strlen($open));
            $current = str_replace('{*}', $current, $with);
            //debug_echo ($current);
        } else {
            $current = $with;
        }
        $in = substr_replace($in, $current, $open_position + strlen($open), $close_position - $open_position - strlen($open));
        $next_position = $open_position + strlen($current) + 1;
        if ($next_position >= strlen($in)) {
            return false;
        }
        if ($limit !== false) {
            $limit--;
        }
        between_replace($open, $close, $in, $with, $limit, $next_position);
        return $in;
    }

    /**
     * Uppercase words that are not separated by spaces, using a custom
     * delimiter or the default.
     *
     *      $str = cstr::ucfirst('content-type'); // returns "Content-Type"
     *
     * @param   string  $string     string to transform
     * @param   string  $delimiter  delimiter to use
     * @uses    CUTF8::ucfirst
     * @return  string
     */
    public static function ucfirst($string, $delimiter = ' ') {
        // Put the keys back the Case-Convention expected
        return implode($delimiter, array_map('CUTF8::ucfirst', explode($delimiter, $string)));
    }

    /**
     * Returns information about the client user agent.
     *
     *     // Returns "Chrome" when using Google Chrome
     *     $browser = Text::user_agent($agent, 'browser');
     *
     * Multiple values can be returned at once by using an array:
     *
     *     // Get the browser and platform with a single call
     *     $info = Text::user_agent($agent, array('browser', 'platform'));
     *
     * When using an array for the value, an associative array will be returned.
     *
     * @param   string  $agent  user_agent
     * @param   mixed   $value  array or string to return: browser, version, robot, mobile, platform
     * @return  mixed   requested information, FALSE if nothing is found
     * @uses    Kohana::$config
     */
    public static function user_agent($agent, $value) {
        if (is_array($value)) {
            $data = array();
            foreach ($value as $part) {
                // Add each part to the set
                $data[$part] = Text::user_agent($agent, $part);
            }

            return $data;
        }

        if ($value === 'browser' OR $value == 'version') {
            // Extra data will be captured
            $info = array();

            // Load browsers
            $browsers = Kohana::$config->load('user_agents')->browser;

            foreach ($browsers as $search => $name) {
                if (stripos($agent, $search) !== FALSE) {
                    // Set the browser name
                    $info['browser'] = $name;

                    if (preg_match('#' . preg_quote($search) . '[^0-9.]*+([0-9.][0-9.a-z]*)#i', $agent, $matches)) {
                        // Set the version number
                        $info['version'] = $matches[1];
                    } else {
                        // No version number found
                        $info['version'] = FALSE;
                    }

                    return $info[$value];
                }
            }
        } else {
            // Load the search group for this type
            $group = CF::config('user_agents');

            foreach ($group as $search => $name) {
                if (stripos($agent, $search) !== FALSE) {
                    // Set the value name
                    return $name;
                }
            }
        }

        // The value requested could not be found
        return FALSE;
    }

    /**
     * Determine if a given string contains a given substring.
     *
     * @param  string  $haystack
     * @param  string|array  $needles
     * @return bool
     */
    public static function contains($haystack, $needles) {
        foreach ((array) $needles as $needle) {
            if ($needle !== '' && mb_strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

}

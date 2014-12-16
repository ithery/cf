<?php

class cutils {

    public static function indent($n, $char = "\t") {
        $res = "";
        for ($i = 0; $i < $n; $i++) {
            $res.=$char;
        }
        return $res;
    }

    public static function br() {
        return "\r\n";
    }

    function add_date($givendate, $day = 0, $mth = 0, $yr = 0) {
        $cd = strtotime($givendate);
        $newdate = date('Y-m-d h:i:s', mktime(date('h', $cd), date('i', $cd), date('s', $cd), date('m', $cd) + $mth, date('d', $cd) + $day, date('Y', $cd) + $yr));
        return $newdate;
    }

    public static function get_month($strdate = "") {
        if (strlen($strdate) > 0) {
            return date('m', strtotime($strdate));
        }
        return date('m');
    }

    public static function get_year($strdate = "") {
        if (strlen($strdate) > 0) {
            return date('Y', strtotime($strdate));
        }
        return date('Y');
    }

    public static function get_day($strdate = "") {
        if (strlen($strdate) > 0) {
            return date('d', strtotime($strdate));
        }
        return date('d');
    }

    public static function get_short_day_name($day, $month, $year) {
        $value = "";
        $timestamp = mktime(0, 0, 0, $month, $day, $year);
        $value = date("D", $timestamp);
        return $value;
    }

    public static function day_count($month = '', $year = '') {
        if (empty($month))
            $month = date('m');
        if (empty($year))
            $year = date('Y');
        return date('d', mktime(0, 0, 0, $month + 1, 0, $year));
    }

    public static function begin_date_month() {
        $d1 = "1";
        $m1 = Date('m');
        $y1 = Date('Y');
        $date1 = $y1 . "-" . $m1 . "-" . $d1;
		$date_format = ccfg::get('date_formatted');
		if($date_format!=null) {
			$date1 = date($date_format,strtotime($date1));
		}
        return $date1;
    }

    public static function last_date_month() {
        $d2 = cutils::day_count();
        $m2 = Date('m');
        $y2 = Date('Y');
        $date2 = $y2 . "-" . $m2 . "-" . $d2;
		$date_format = ccfg::get('date_formatted');
		if($date_format!=null) {
			$date2 = date($date_format,strtotime($date2));
		}
        return $date2;
    }

    public static function randmd5() {
        $rand = rand(0, 9999);
        $base = date('YmdHis') . $rand;
        return md5($rand);
    }

    public static function format_filesize($size) {
        $unit = array('b', 'kb', 'mb', 'gb', 'tb', 'pb');

        return @round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . ' ' . $unit[$i];
    }

    public static function thousand_separator($rp) {
        return ctransform::thousand_separator($rp);
    }

    public static function indonesian_currency_string($val) {

        function get_under_1000($val) {
            $C_NUMBER = Array('', 'Satu', 'Dua', 'Tiga', 'Empat', 'Lima', 'Enam', 'Tujuh', 'Delapan', 'Sembilan');
            $res = '';
            if ($val >= 1000)
                return $res;
            //process hundred
            $tempv = $val;
            if ($tempv >= 100) {
                if (floor($tempv / 100) == 1) {
                    $res = $res . ' Seratus';
                } else {
                    $res .= ' ' . $C_NUMBER[floor($tempv / 100)] . ' Ratus';
                }
            }
            //process ten
            $tempv = $val % 100;
            if ($tempv >= 10) {
                if (floor($tempv / 10) == 1) {
                    if ($tempv % 10 == 0) {
                        $res .= ' Sepuluh';
                    } else if ($tempv % 10 == 1) {
                        $res .= ' Sebelas';
                    } else {
                        $res .= ' ' . $C_NUMBER[$tempv % 10] . ' Belas';
                    }
                } else {
                    $res .= ' ' . $C_NUMBER[floor($tempv / 10)] . ' Puluh';
                    if ($tempv % 10 > 0)
                        $res .= ' ' . $C_NUMBER[$tempv % 10];
                }
            } else {
                if ($tempv % 10 > 0)
                    $res .= ' ' . $C_NUMBER[$tempv % 10];
            }
            $res = trim($res);
            return $res;
        }

        $res = '';
        $tempval = $val;
        if ($tempval >= 1000000000000) {
            $temp_under_1000 = floor($tempval / 1000000000000);
            $res = $res . ' ' . get_under_1000($temp_under_1000) . ' Triliun';
        }
        $tempval = $val % 1000000000000;
        if ($tempval >= 1000000000) {
            $temp_under_1000 = floor($tempval / 1000000000);
            $res = $res . ' ' . get_under_1000($temp_under_1000) . ' Miliar';
        }
        $tempval = $val % 1000000000;
        if (floor($val / 1000000) > 0) {
            $temp_under_1000 = floor($tempval / 1000000);
            $res = $res . ' ' . get_under_1000($temp_under_1000) . ' Juta';
        }
        $tempval = $val % 1000000;
//		if (floor($val / 1000) > 0)  {
        if ($tempval >= 1000) {
            $temp_under_1000 = floor($tempval / 1000);
            if ($temp_under_1000 == 1) {
                $res = $res . ' Seribu';
            } else {
                $res = $res . ' ' . get_under_1000($temp_under_1000) . ' Ribu';
            }
        }
        $tempval = $val % 1000;
        $res = $res . ' ' . get_under_1000($tempval);
        $result = trim($res);
        return $result;
    }

    public static function day_list() {
        $day = array();
        for ($i = 1; $i <= 31; $i++) {
            $day["" . $i] = $i;
        }
        return $day;
    }

    public static function day_name_list() {
        $timestamp = strtotime('next Sunday');
        $day_list = array();
        for ($i = 0; $i < 7; $i++) {
            $day_list[] = strftime('%A', $timestamp);
            $timestamp = strtotime('+1 day', $timestamp);
        }
        return $day_list;
    }

    public static function month_list($clang = true) {
        return cstatic::month_list($clang);
    }

    public static function month_name($month, $clang = true) {
        $list = cstatic::month($clang);
        if (isset($list[$month])) {
            return $list[$month];
        }
        return "Unknown";
    }

    public static function year_list($start = "", $end = "") {
        if (strlen($start) == 0)
            $start = "1900";
        if (strlen($end) == 0)
            $end = cutils::get_year();
        if ($start > $end) {
            return array();
        }
        $list = array();
        for ($i = $start; $i <= $end; $i++) {
            $list[$i] = $i;
        }
        return $list;
    }

    public static function sanitize_msisdn($msisdn, $prefix = "62") {
        $ret = $msisdn;
        if (strlen($ret) > 0) {
            if (substr($ret, 0, 1) == "+") {
                $ret = substr($ret, 1);
            }
            if (substr($ret, 0, 1) == "0") {
                $ret = $prefix . substr($ret, 1);
            }
        }
        return $ret;
    }

    public static function sanitize($string = '', $is_filename = FALSE) {
        // Replace all weird characters with dashes
        $string = preg_replace('/[^\w\-' . ($is_filename ? '~_\.' : '') . ']+/u', '-', $string);

        // Only allow one dash separator at a time (and make string lowercase)
        return mb_strtolower(preg_replace('/--+/u', '-', $string), 'UTF-8');
    }

    public static function month_romawi($bln) {
        $result = "";
        switch ($bln) {
            case "1": $result = "I";
                break;
            case "2": $result = "II";
                break;
            case "3": $result = "III";
                break;
            case "4": $result = "IV";
                break;
            case "5": $result = "V";
                break;
            case "6": $result = "VI";
                break;
            case "7": $result = "VII";
                break;
            case "8": $result = "VIII";
                break;
            case "9": $result = "IX";
                break;
            case "10": $result = "X";
                break;
            case "11": $result = "XI";
                break;
            case "12": $result = "XII";
                break;
        }
        return $result;
    }

    public static function trim_csv($text) {
        if ($text == null)
            $text = "";
        $text = str_replace("\r\n", "", $text);
        $text = str_replace("\r", "", $text);
        $text = str_replace("\n", "", $text);
        return $text;
    }

    public static function year_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $years;
    }

    public static function month_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $months;
    }

    public static function day_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $days;
    }

    public static function hour_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $hours;
    }

    public static function minute_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $minutes;
    }

    public static function second_diff($from, $to = '') {
        if ($to == '') {
            $to = time();
        }
        if (is_string($from))
            $from = strtotime($from);
        if (is_string($to))
            $to = strtotime($to);
        $from = new DateTime(date('Y-m-d H:i:s', $from));
        $to = new DateTime(date('Y-m-d H:i:s', $to));
        $seconds = (($to->format('U') - $from->format('U')));
        $minutes = (($to->format('U') - $from->format('U')) / (60));
        $hours = (($to->format('U') - $from->format('U')) / (60 * 60));
        $days = (($to->format('U') - $from->format('U')) / (60 * 60 * 24));
        $months = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30));
        $years = (($to->format('U') - $from->format('U')) / (60 * 60 * 24 * 30 * 12));
        return $second;
    }

}

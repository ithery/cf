<?php

defined('SYSPATH') or die('No direct access allowed.');

 //@codingStandardsIgnoreStart
trait CApp_Trait_GenerateCode {
    /**
     * @param string $keyCounter
     *
     * @return string
     */
    public static function _get_next_counter($keyCounter) {
        $db = c::db();
        $next_counter = 1;
        $is_insert = 1;
        $q = 'select case when counter is null then 1 else counter+1 end as next_counter from sys_counter where `key`=' . $db->escape($keyCounter) . ' for update';

        $r = $db->query($q);

        if ($r->count() > 0) {
            $next_counter = $r[0]->next_counter;
            $is_insert = 0;
        }
        $cmd = '';
        if ($is_insert == 1) {
            $cmd = 'insert into sys_counter(`key`,counter,created) values (' . $db->escape($keyCounter) . ',1,now());';
        } else {
            $cmd = 'update sys_counter set counter = counter+1, updated = now() where `key` = ' . $db->escape($keyCounter) . '';
        }
        $db->query($cmd);

        $q = 'select case when counter is null then 1 else counter end as next_counter from sys_counter where `key`=' . $db->escape($keyCounter) . '';

        $r = $db->query($q);

        if ($r->count() > 0) {
            $next_counter = $r[0]->next_counter;
            $is_insert = 0;
        }

        return $next_counter;
    }

    /**
     * @param string $keyFormat
     *
     * @return string
     */
    public static function _get_key_counter($keyFormat) {
        $result = $keyFormat;
        preg_match_all("/{([\w]*)}/", $keyFormat, $matches, PREG_SET_ORDER);

        foreach ($matches as $val) {
            $str = $val[1]; //matches str without bracket {}
            $b_str = $val[0]; //matches str with bracket {}
            switch ($str) {
                case 'yyyy':
                    $result = str_replace('{yyyy}', date('Y'), $result);

                    break;
                case 'yy':
                    $result = str_replace('{yy}', date('y'), $result);

                    break;
                case 'mm':
                    $result = str_replace('{mm}', date('m'), $result);

                    break;
                case 'dd':
                    $result = str_replace('{dd}', date('d'), $result);

                    break;
                case 'MM':
                    $result = str_replace('{MM}', cutils::month_romawi(date('m')), $result);

                    break;
            }
        }

        return $result;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public static function _get_next_code($key) {
        $key_format = self::get_format($key);

        return self::_get_next_code_from_format($key_format);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected static function get_format($key) {
        $db = c::db();
        $ukey = strtoupper($key);
        $q = "select `format` from sys_format_auto_code where `key`='" . $ukey . "'";
        $r = $db->query($q);
        $result = '';
        if ($r->count() > 0) {
            $result = $r[0]->format;
        } else {
            throw new Exception('No Format available for this key[' . $key . ']');
        }

        return $result;
    }

    /**
     * @param string $keyFormat
     *
     * @return string
     */
    public static function _get_next_code_from_format($keyFormat) {
        $key_counter = self::_get_key_counter($keyFormat);
        $result = $key_counter;
        $counter = self::_get_next_counter($key_counter);
        preg_match_all("/{([\w]*)}/", $keyFormat, $matches, PREG_SET_ORDER);
        foreach ($matches as $val) {
            $str = $val[1]; //matches str without bracket {}
            $b_str = $val[0]; //matches str with bracket {}
            $len_counter = strlen($str);
            $pad_counter = str_pad($counter, $len_counter, '0', STR_PAD_LEFT);
            $result = str_replace($b_str, $pad_counter, $result);
        }

        return $result;
    }
}

//@codingStandardsIgnoreEnd

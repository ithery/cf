<?php

//@codingStandardsIgnoreStart
class cphp {
    public static function string_value($val, $level = 0) {
        $str = '';
        $eol = PHP_EOL;
        $indent = cutils::indent($level, "\t");
        if (is_array($val)) {
            $str .= 'array(' . $eol;
            $indent2 = cutils::indent($level + 1, "\t");
            foreach ($val as $k => $v) {
                $kSlashes = addcslashes($k, '\'\\');
                $str .= $indent2 . "'" . $kSlashes . "'=>";
                $str .= self::string_value($v, $level + 1);
                $str .= ',' . $eol;
            }

            $str .= $indent . ')';
        } elseif (is_null($val)) {
            $str .= 'NULL';
        } elseif (is_bool($val)) {
            $str .= ($val === true ? 'TRUE' : 'FALSE');
        } else {
            $str .= "'" . addcslashes($val, '\'\\') . "'";
        }
        return $str;
    }

    public static function save_value($value, $filename = null) {
        $val = '<?php ' . PHP_EOL . 'return ' . cphp::string_value($value) . ';';
        if ($filename != null) {
            cfs::atomic_write($filename, $val);
            //file_put_contents($filename, $val);
        }
        return $val;
    }

    public static function load_value($filename) {
        if (!file_exists($filename)) {
            throw new Exception($filename . ' Not found');
        }
        return include $filename;
    }
}
//@codingStandardsIgnoreEnd

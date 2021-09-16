<?php

//@codingStandardsIgnoreStart
/**
 * @deprecated since 1.2, see CFile
 * @see CFile
 */
class cphp {
    /**
     * @param mixed $val
     * @param int   $level
     *
     * @return string
     *
     * @deprecated since 1.2, see CFile::phpValue
     */
    public static function string_value($val, $level = 0) {
        return CFile::phpValue($val, $level);
    }

    /**
     * @param mixed  $value
     * @param string $filename
     *
     * @return int|bool
     *
     * @deprecated since 1.2, see CFile::putPhpValue
     */
    public static function save_value($value, $filename = null) {
        return CFile::putPhpValue($filename, $value);
    }

    /**
     * @param string $filename
     *
     * @return mixed
     *
     * @deprecated since 1.2, see CFile::getRequire
     */
    public static function load_value($filename) {
        return CFile::getRequire($filename);
    }
}
//@codingStandardsIgnoreEnd

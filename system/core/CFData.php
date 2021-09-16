<?php

defined('SYSPATH') or die('No direct access allowed.');

final class CFData {
    public static function path() {
        $dir = DOCROOT . 'data' . DIRECTORY_SEPARATOR . '';
        return $dir;
    }

    public static function domain($domain) {
        return CFData::get($domain, 'domain');
    }

    public static function get($dataName, $folder = '') {
        $file = self::path();
        if (strlen($folder) > 0) {
            $folder = explode('/', $folder);
            foreach ($folder as $row) {
                if (strlen($row) == 0) {
                    continue;
                }
                $file .= $row . DIRECTORY_SEPARATOR;
            }
        }
        $file .= $dataName;

        if (file_exists($file . EXT)) {
            return self::load_value($file . EXT);
        }
        $dataNameExploded = explode('.', $dataName);
        if (count($dataNameExploded) > 0) {
            $fileWildcard = '$.' . implode('.', array_slice($dataNameExploded, 1));

            if (file_exists($fileWildcard . EXT)) {
                return self::load_value($fileWildcard . EXT);
            }
        }
        return null;

        //$content = file_get_contents($file);
        //return cjson::decode($content);
    }

    public static function set($data_name, $data, $folder = '') {
        $file = self::path();
        if (strlen($folder) > 0) {
            $folder = explode('/', $folder);
            foreach ($folder as $row) {
                if (strlen($row) == 0) {
                    continue;
                }
                $file .= $row . DIRECTORY_SEPARATOR;
                if (!is_dir($file)) {
                    mkdir($file);
                }
            }
        }
        $file .= $data_name . EXT;
        //$json = cjson::encode($data);
        self::save_value($data, $file);
        //file_put_contents($file,$json);
        return true;
    }

    /**
     * Delete data file
     *
     * @param string $dataName
     * @param string $folder
     *
     * @return void
     */
    public static function delete($dataName, $folder = '') {
        $file = self::path();
        if (strlen($folder) > 0) {
            $folder = explode('/', $folder);
            foreach ($folder as $row) {
                if (strlen($row) == 0) {
                    continue;
                }
                $file .= $row . DIRECTORY_SEPARATOR;
                if (!is_dir($file)) {
                    throw new CException('Error, :path is not directory', [':path' => $file]);
                }
            }
        }
        $file .= $dataName . EXT;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * String value
     *
     * @param mixed   $val
     * @param integer $level
     *
     * @return string
     */
    // @codingStandardsIgnoreStart
    public static function string_value($val, $level = 0) {
        // @codingStandardsIgnoreEnd
        $indentString = '    ';
        $str = '';
        $eol = PHP_EOL;
        $indent = cutils::indent($level, $indentString);
        if (is_array($val)) {
            $str .= 'array(' . $eol;
            $indent2 = cutils::indent($level + 1, $indentString);
            foreach ($val as $k => $v) {
                $str .= $indent2 . "'" . addslashes($k) . "'=>";
                $str .= self::string_value($v, $level + 1);
                $str .= ',' . $eol;
            }

            $str .= $indent . ')';
        } elseif (is_null($val)) {
            $str .= 'NULL';
        } elseif (is_bool($val)) {
            $str .= ($val === true ? 'TRUE' : 'FALSE');
        } else {
            $str .= "'" . addslashes($val) . "'";
        }
        return $str;
    }

    // @codingStandardsIgnoreStart
    public static function save_value($value, $filename = null) {
        // @codingStandardsIgnoreEnd
        $val = '<?php ' . PHP_EOL . 'return ' . static::string_value($value) . ';';
        if ($filename != null) {
            file_put_contents($filename, $val);
        }
        return $val;
    }

    // @codingStandardsIgnoreStart
    public static function load_value($filename) {
        // @codingStandardsIgnoreEnd
        return include $filename;
    }
}

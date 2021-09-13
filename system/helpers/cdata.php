<?php

//@codingStandardsIgnoreStart
class cdata {
    public static function path() {
        $dir = DOCROOT . 'data' . DIRECTORY_SEPARATOR . '';
        return $dir;
    }

    public static function get($data_name, $folder = '') {
        $file = cdata::path();
        if (strlen($folder) > 0) {
            $folder = explode('/', $folder);
            foreach ($folder as $row) {
                if (strlen($row) == 0) {
                    continue;
                }
                $file .= $row . DS;
            }
        }
        $file .= $data_name;
        if (!file_exists($file . EXT)) {
            return null;
        }
        return cphp::load_value($file . EXT);
        //$content = file_get_contents($file);
        //return cjson::decode($content);
    }

    public static function set($data_name, $data, $folder = '') {
        $file = cdata::path();
        if (strlen($folder) > 0) {
            $folder = explode('/', $folder);
            foreach ($folder as $row) {
                if (strlen($row) == 0) {
                    continue;
                }
                $file .= $row . DS;
                if (!is_dir($file)) {
                    mkdir($file);
                }
            }
        }
        $file .= $data_name . EXT;
        //$json = cjson::encode($data);
        cphp::save_value($data, $file);
        //file_put_contents($file,$json);
        return true;
    }

    public static function delete($data_name, $folder = '') {
        $file = cdata::path();
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
        if (file_exists($file)) {
            unlink($file);
        }
    }
}

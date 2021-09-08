<?php

class CApp_Data {
    public static function haveData($dataName) {
        $dataFile = CF::findFile('data', $dataName);
        return is_file($dataFile);
    }

    public static function getData($dataName) {
        $dataFile = CF::findFile('data', $dataName);
        $data = null;
        if (file_exists($dataFile)) {
            $data = include $dataFile;
        }
        return $data;
    }

    public static function getLanguageCode($iso = 'ISO6391') {
        $dataName = 'LanguageCode/' . $iso;
        if (!self::haveData($dataName)) {
            throw new CStorage_Exception_FileNotFoundException('File Data ' . $dataName . ' not found');
        }
        return self::getData($dataName);
    }
}

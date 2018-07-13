<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CApp_Data {

    public static function haveData($dataName) {
        $dataFile = CF::find_file('data', $dataName);
        return is_file($dataFile);
    }

    public static function getData($dataName) {
        $dataFile = CF::find_file('data', $dataName);
        $data = null;
        if (file_exists($dataFile)) {
            $data = include $dataFile;
        }
        return $data;
    }

    public static function getLanguageCode($iso = 'ISO6391') {
        $dataName = 'LanguageCode/' . $iso;
        if (!self::haveData($dataName)) {
            throw new CApp_Exception_FileNotFoundException('File Data ' . $dataName . ' not found');
        }
        return self::getData($dataName);
    }

}

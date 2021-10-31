<?php

class CResources_Encode {
    const DIGIT = 4;

    public static function encode($file_name) {
        $str = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
        shuffle($str);
        $salt = '';
        foreach (array_rand($str, self::DIGIT) as $key) {
            $salt .= $str[$key];
        }

        /* hardcoding salt for browser cache */
        $salt = '6262';

        $result = '';
        for ($i = 0; $i < strlen($file_name); $i++) {
            $result .= $file_name[$i] ^ $salt[$i % self::DIGIT];
        }
        $result = base64_encode($salt . $result);
        return str_replace('/', '_', $result);
        ;
    }
}

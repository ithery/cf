<?php

class CResources_Encode {

    const _digit = 4;

    public static function encode($file_name) {
//           s return $file_name;
        $str = str_split("ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789");
        shuffle($str);
        $salt = '';
        foreach (array_rand($str, self::_digit) as $key) {
            $salt .= $str[$key];
        }

        /* hardcoding salt for browser cache */
        $salt = '6262';


//            $file_ext = ".".pathinfo($file_name, PATHINFO_EXTENSION);
//            $file_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);

        $result = '';
        for ($i = 0; $i < strlen($file_name); $i++) {
            $result .= $file_name[$i] ^ $salt[$i % self::_digit];
        }
        $result = base64_encode($salt . $result);
        return str_replace("/", "_", $result);
        ;
    }

}

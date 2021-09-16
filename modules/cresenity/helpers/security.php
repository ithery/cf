<?php

//@codingStandardsIgnoreStart
class security {
    const _digit = 4;

    public static function encrypt($plain_text) {
        $encription_key = ccfg::get('encription_key');
        if (strlen($encription_key) == 0) {
            $encription_key = 'DEFAULT';
        }
        $str = str_split('ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789');
        shuffle($str);
        $salt = '';
        foreach (array_rand($str, self::_digit) as $key) {
            $salt .= $str[$key];
        }

        $count_encription_key = count($encription_key);
        $chiper_text = '';
        for ($i = 0; $i < strlen($plain_text); $i++) {
            $chiper_text .= $plain_text[$i] ^ $encription_key[$i % $count_encription_key];
        }

        $chiper_text = base64_encode($chiper_text);
        $chiper_text = str_replace('/', '_', $chiper_text);
        return $chiper_text;
    }

    public static function decrypt($chiper_text) {
        $encription_key = ccfg::get('encription_key');
        if (strlen($encription_key) == 0) {
            $encription_key = 'DEFAULT';
        }
        $chiper_text = str_replace('_', '/', $chiper_text);
        $chiper_text = base64_decode($chiper_text);
        $count_encription_key = count($encription_key);

        $decrypt_text = '';
        for ($i = 0; $i < strlen($chiper_text); $i++) {
            $decrypt_text .= $chiper_text[$i] ^ $encription_key[$i % $count_encription_key];
        }
        return $decrypt_text;
    }
}

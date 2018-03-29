<?php

    class CResources_Decode{
        const _digit=4;

        public static function decode($file_name){
            $file_name = str_replace("_", "/", $file_name);
//            $file_ext = ".".pathinfo($file_name, PATHINFO_EXTENSION);
//            $file_name = preg_replace('/\\.[^.\\s]{3,4}$/', '', $file_name);
            $temp = base64_decode($file_name);
            $salt = substr($temp,0,self::_digit);
            $temp = substr($temp,self::_digit);
            $result = '';
            for($i=0;$i<strlen($temp);$i++){
                $result .= $temp[$i] ^ $salt[$i%self::_digit];
            }
            return $result;
        }
    }


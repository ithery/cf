<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Dec 12, 2014
     * @license http://piposystem.com Piposystem
     */

    class cdate {
        
        public static function age($birthday,$return_year = true){

            $diff_year = (date("md",  strtotime($birthday)) > date("md") ? (date("Y")-date("Y",strtotime($birthday))-1):(date("Y")-date("Y",strtotime($birthday))));
            $diff_month = (date("d",  strtotime($birthday)) > date("d") ? (date("m")-date("m",strtotime($birthday))-1):(date("m")-date("m",strtotime($birthday))));
            $result = $diff_year;
            if($return_year !== true) {
                $result = ($diff_year*12)+$diff_month;
            echo $birthday ."::" .$result ."===";
            }
            return $result;
        }
    }
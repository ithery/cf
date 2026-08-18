<?php

class CReport_Jasper_Utils_TimeUtils {
    public static function timeToSecond($time) {
        if ($time instanceof DateTime) {
            return $time->getTimestamp();
        }

        return strtotime($time);
    }

    public static function secondToTime($second) {
        return CCarbon::createFromTimestamp($second);
    }
}

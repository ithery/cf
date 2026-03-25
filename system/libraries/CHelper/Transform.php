<?php

defined('SYSPATH') or die('No direct access allowed.');

class CHelper_Transform {
    public static function formatSize($bytes) {
        return CHelper_Formatter::formatSize($bytes);
    }

    public static function formatNumber($number) {
        return CHelper_Formatter::formatNumber($number);
    }

    public static function formatCurrency($number) {
        return CHelper_Formatter::formatCurrency($number);
    }

    public static function unformatDate($x) {
        return date('Y-m-d', strtotime($x));
    }
}

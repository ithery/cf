<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
use Carbon\Carbon;

class CBackup_Helper {

    public static function formatHumanReadableSize($sizeInBytes) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        if ($sizeInBytes === 0) {
            return '0 ' . $units[1];
        }
        for ($i = 0; $sizeInBytes > 1024; $i++) {
            $sizeInBytes /= 1024;
        }
        return round($sizeInBytes, 2) . ' ' . $units[$i];
    }

    public static function formatEmoji($bool) {

        $unicodeChar = '\u274c';
        if ($bool) {
            $unicodeChar = '\u2705';
        }

        return json_decode('"' . $unicodeChar . '"');
    }

    public static function formatAgeInDays(Carbon $date) {
        return number_format(round($date->diffInMinutes() / (24 * 60), 2), 2) . ' (' . $date->diffForHumans() . ')';
    }

}

<?php

class CChart {
    public static function pieChart($width = 500, $height = 500) {
        return new CChart_Chart_PieChart($width, $height);
    }
}

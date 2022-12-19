<?php

class CChart {
    const DIRECTION_VERTICAL = 'vertical';

    const DIRECTION_HORIZONTAL = 'horizontal';

    public static function pieChart() {
        return new CChart_Chart_PieChart();
    }

    public static function lineChart() {
        return new CChart_Chart_LineChart();
    }

    public static function barChart() {
        return new CChart_Chart_BarChart();
    }
}

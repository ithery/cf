<?php

class CChart {
    const DIRECTION_VERTICAL = 'v';

    const DIRECTION_HORIZONTAL = 'h';

    const ALIGN_LEFT = 'left';

    const ALIGN_CENTER = 'center';

    const ALIGN_RIGHT = 'right';

    const POSITION_TOP = 't';

    const POSITION_BOTTOM = 'b';

    const POSITION_LEFT = 'l';

    const POSITION_RIGHT = 'r';

    const TYPE_LINE = 'line';

    const TYPE_BAR = 'bar';

    const TYPE_PIE = 'pie';

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

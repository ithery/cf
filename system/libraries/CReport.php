<?php

class CReport {
    const ORIENTATION_LANDSCAPE = 'landscape';

    const ORIENTATION_PORTRAIT = 'portrait';

    const TEXT_ALIGNMENT_LEFT = 'left';

    const TEXT_ALIGNMENT_CENTER = 'center';

    const TEXT_ALIGNMENT_RIGHT = 'right';

    const VERTICAL_ALIGNMENT_TOP = 'top';

    const VERTICAL_ALIGNMENT_MIDDLE = 'middle';

    const VERTICAL_ALIGNMENT_BOTTOM = 'bottom';

    const LINE_STYLE_DASHED = 'dashed';

    const LINE_STYLE_SOLID = 'solid';

    const LINE_STYLE_DOTTED = 'dotted';

    const LINE_STYLE_DOUBLE = 'double';

    /**
     * @param string $jrxml
     * @param array  $param
     *
     * @return CReport_Jasper
     */
    public static function jasper($jrxml, array $param = []) {
        return new CReport_Jasper($jrxml, $param);
    }

    /**
     * @return CReport_Builder
     */
    public static function builder() {
        return new CReport_Builder();
    }
}

<?php

class CReport {
    const ORIENTATION_LANDSCAPE = 'landscape';

    const ORIENTATION_PORTRAIT = 'portrait';

    const TEXT_ALIGNMENT_LEFT = 'left';

    const TEXT_ALIGNMENT_CENTER = 'center';

    const TEXT_ALIGNMENT_RIGHT = 'right';

    const TEXT_ALIGNMENT_JUSTIFY = 'justify';

    const VERTICAL_ALIGNMENT_TOP = 'top';

    const VERTICAL_ALIGNMENT_MIDDLE = 'middle';

    const VERTICAL_ALIGNMENT_BOTTOM = 'bottom';

    const HORIZONTAL_ALIGNMENT_LEFT = 'left';

    const HORIZONTAL_ALIGNMENT_CENTER = 'center';

    const HORIZONTAL_ALIGNMENT_RIGHT = 'right';

    const LINE_STYLE_DASHED = 'dashed';

    const LINE_STYLE_SOLID = 'solid';

    const LINE_STYLE_DOTTED = 'dotted';

    const LINE_STYLE_DOUBLE = 'double';

    const SPLIT_TYPE_IMMEDIATE = 'immediate';

    const SPLIT_TYPE_PREVENT = 'prevent';

    const SPLIT_TYPE_STRETCH = 'stretch';

    /**
     * A constant value specifying that if the actual image is larger than the image element size, it will be cut off so that it keeps its original resolution, and only the region that fits the specified size will be displayed.
     */
    const SCALE_IMAGE_CLIP = 'clip';

    /**
     * A constant value specifying that if the dimensions of the actual image do not fit those specified for the image element that displays it, the image can be forced to obey them and stretch itself so that it fits in the designated output area.
     */
    const SCALE_IMAGE_FILL_FRAME = 'fillFrame';

    /**
     * A scale image type that instructs the engine to stretch the image height to fit the actual height of the image.
     */
    const SCALE_IMAGE_REAL_HEIGHT = 'realHeight';

    /**
     * A scale image type that instructs the engine to stretch the image height to fit the actual height of the image.
     */
    const SCALE_IMAGE_REAL_SIZE = 'realSize';

    /**
     * A constant value specifying that if the actual image does not fit into the image element, it can be adapted to those dimensions without needing to change its original proportions.
     */
    const SCALE_IMAGE_RETAIN_SHAPE = 'retainShape';

    const DATA_TYPE_FLOAT = 'float';

    const DATA_TYPE_STRING = 'string';

    const DATA_TYPE_INT = 'int';

    const DATA_TYPE_BOOL = 'bool';

    const DATA_TYPE_DATETIME = 'datetime';

    const CALCULATION_SYSTEM = 'system';

    const CALCULATION_SUM = 'sum';

    const CALCULATION_AVG = 'avg';

    /**
     * The variable is reinitialized at the beginning of each new column.
     */
    const RESET_TYPE_COLUMN = 'column';

    /**
     * The variable is reinitialized every time the group breaks.
     */
    const RESET_TYPE_GROUP = 'group';

    /**
     * Used internally by the master report page variables to allow the variables to be used in text fields with Auto evaluation time.
     */
    const RESET_TYPE_MASTER = 'master';

    /**
     * The variable will never be initialized using its initial value expression and will only contain values obtained by evaluating the variable's expression.
     */
    const RESET_TYPE_NONE = 'none';

    /**
     * The variable is reinitialized at the beginning of each new page.
     */
    const RESET_TYPE_PAGE = 'page';

    /**
     * The variable is initialized only once, at the beginning of the report filling process, with the value returned by the variable's initial value expression.
     */
    const RESET_TYPE_REPORT = 'report';

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

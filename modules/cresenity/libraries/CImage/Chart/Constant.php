<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 30, 2019, 2:26:15 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CImage_Chart_Constant {

    /**
     * pBubble
     */
    const BUBBLE_SHAPE_ROUND = 700001;
    const BUBBLE_SHAPE_SQUARE = 700002;

    /**
     * pData
     */
    /* Axis configuration */
    const AXIS_FORMAT_DEFAULT = 680001;
    const AXIS_FORMAT_TIME = 680002;
    const AXIS_FORMAT_DATE = 680003;
    const AXIS_FORMAT_METRIC = 680004;
    const AXIS_FORMAT_CURRENCY = 680005;
    const AXIS_FORMAT_TRAFFIC = 680006;
    const AXIS_FORMAT_CUSTOM = 680007;
    /* Axis position */
    const AXIS_POSITION_LEFT = 681001;
    const AXIS_POSITION_RIGHT = 681002;
    const AXIS_POSITION_TOP = 681001;
    const AXIS_POSITION_BOTTOM = 681002;
    /* Families of data points */
    const SERIE_SHAPE_FILLEDCIRCLE = 681011;
    const SERIE_SHAPE_FILLEDTRIANGLE = 681012;
    const SERIE_SHAPE_FILLEDSQUARE = 681013;
    const SERIE_SHAPE_FILLEDDIAMOND = 681017;
    const SERIE_SHAPE_CIRCLE = 681014;
    const SERIE_SHAPE_TRIANGLE = 681015;
    const SERIE_SHAPE_SQUARE = 681016;
    const SERIE_SHAPE_DIAMOND = 681018;
    /* Axis position */
    const AXIS_X = 682001;
    const AXIS_Y = 682002;
    /* Define value limits */
    const ABSOLUTE_MIN = -10000000000000;
    const ABSOLUTE_MAX = 10000000000000;
    /* Replacement to the PHP null keyword */
    const VOID = 0.123456789;

    /* Euro symbol for GD fonts */

    public static function EURO_SYMBOL() {
        return utf8_encode("&#8364;");
    }

    /**
     * pDraw
     */
    const DIRECTION_VERTICAL = 690001;
    const DIRECTION_HORIZONTAL = 690002;
    const SCALE_POS_LEFTRIGHT = 690101;
    const SCALE_POS_TOPBOTTOM = 690102;
    const SCALE_MODE_FLOATING = 690201;
    const SCALE_MODE_START0 = 690202;
    const SCALE_MODE_ADDALL = 690203;
    const SCALE_MODE_ADDALL_START0 = 690204;
    const SCALE_MODE_MANUAL = 690205;
    const SCALE_SKIP_NONE = 690301;
    const SCALE_SKIP_SAME = 690302;
    const SCALE_SKIP_NUMBERS = 690303;
    const TEXT_ALIGN_TOPLEFT = 690401;
    const TEXT_ALIGN_TOPMIDDLE = 690402;
    const TEXT_ALIGN_TOPRIGHT = 690403;
    const TEXT_ALIGN_MIDDLELEFT = 690404;
    const TEXT_ALIGN_MIDDLEMIDDLE = 690405;
    const TEXT_ALIGN_MIDDLERIGHT = 690406;
    const TEXT_ALIGN_BOTTOMLEFT = 690407;
    const TEXT_ALIGN_BOTTOMMIDDLE = 690408;
    const TEXT_ALIGN_BOTTOMRIGHT = 690409;
    const POSITION_TOP = 690501;
    const POSITION_BOTTOM = 690502;
    const LABEL_POS_LEFT = 690601;
    const LABEL_POS_CENTER = 690602;
    const LABEL_POS_RIGHT = 690603;
    const LABEL_POS_TOP = 690604;
    const LABEL_POS_BOTTOM = 690605;
    const LABEL_POS_INSIDE = 690606;
    const LABEL_POS_OUTSIDE = 690607;
    const ORIENTATION_HORIZONTAL = 690701;
    const ORIENTATION_VERTICAL = 690702;
    const ORIENTATION_AUTO = 690703;
    const LEGEND_NOBORDER = 690800;
    const LEGEND_BOX = 690801;
    const LEGEND_ROUND = 690802;
    const LEGEND_VERTICAL = 690901;
    const LEGEND_HORIZONTAL = 690902;
    const LEGEND_FAMILY_BOX = 691051;
    const LEGEND_FAMILY_CIRCLE = 691052;
    const LEGEND_FAMILY_LINE = 691053;
    const DISPLAY_AUTO = 691001;
    const DISPLAY_MANUAL = 691002;
    const LABELING_ALL = 691011;
    const LABELING_DIFFERENT = 691012;
    const BOUND_MIN = 691021;
    const BOUND_MAX = 691022;
    const BOUND_BOTH = 691023;
    const BOUND_LABEL_POS_TOP = 691031;
    const BOUND_LABEL_POS_BOTTOM = 691032;
    const BOUND_LABEL_POS_AUTO = 691033;
    const CAPTION_LEFT_TOP = 691041;
    const CAPTION_RIGHT_BOTTOM = 691042;
    const GRADIENT_SIMPLE = 691051;
    const GRADIENT_EFFECT_CAN = 691052;
    const LABEL_TITLE_NOBACKGROUND = 691061;
    const LABEL_TITLE_BACKGROUND = 691062;
    const LABEL_POINT_NONE = 691071;
    const LABEL_POINT_CIRCLE = 691072;
    const LABEL_POINT_BOX = 691073;
    const ZONE_NAME_ANGLE_AUTO = 691081;
    const PI = 3.14159265;
    const ALL = 69;
    const NONE = 31;
    const AUTO = 690000;
    const OUT_OF_SIGHT = -10000000000000;

    /**
     * pImage
     */
    /* Image map handling */
    const IMAGE_MAP_STORAGE_FILE = 680001;
    const IMAGE_MAP_STORAGE_SESSION = 680002;
    /* Last generated chart layout */
    const CHART_LAST_LAYOUT_REGULAR = 680011;
    const CHART_LAST_LAYOUT_STACKED = 680012;

    /* ImageMap string delimiter */

    public static function IMAGE_MAP_DELIMITER() {
        return chr(1);
    }

    /**
     * pIndicator
     */
    const INDICATOR_CAPTION_DEFAULT = 700001;
    const INDICATOR_CAPTION_EXTENDED = 700002;
    const INDICATOR_CAPTION_INSIDE = 700011;
    const INDICATOR_CAPTION_BOTTOM = 700012;
    const INDICATOR_VALUE_BUBBLE = 700021;
    const INDICATOR_VALUE_LABEL = 700022;

    /**
     * pPie
     */
    /* Class return codes */
    const PIE_NO_ABSCISSA = 140001;
    const PIE_NO_DATASERIE = 140002;
    const PIE_SUMISNULL = 140003;
    const PIE_RENDERED = 140000;
    const PIE_LABEL_COLOR_AUTO = 140010;
    const PIE_LABEL_COLOR_MANUAL = 140011;
    const PIE_VALUE_NATURAL = 140020;
    const PIE_VALUE_PERCENTAGE = 140021;
    const PIE_VALUE_INSIDE = 140030;
    const PIE_VALUE_OUTSIDE = 140031;

    /**
     * pRadar
     */
    const SEGMENT_HEIGHT_AUTO = 690001;
    const RADAR_LAYOUT_STAR = 690011;
    const RADAR_LAYOUT_CIRCLE = 690012;
    const RADAR_LABELS_ROTATED = 690021;
    const RADAR_LABELS_HORIZONTAL = 690022;

    /**
     * pScatter
     */
    const SCATTER_MISSING_X_SERIE = 190001;
    const SCATTER_MISSING_Y_SERIE = 190002;

    /**
     * pSplit
     */
    const TEXT_POS_TOP = 690001;
    const TEXT_POS_RIGHT = 690002;

    /**
     * pSpring
     */
    const NODE_TYPE_FREE = 690001;
    const NODE_TYPE_CENTRAL = 690002;
    const NODE_SHAPE_CIRCLE = 690011;
    const NODE_SHAPE_TRIANGLE = 690012;
    const NODE_SHAPE_SQUARE = 690013;
    const ALGORITHM_RANDOM = 690021;
    const ALGORITHM_WEIGHTED = 690022;
    const ALGORITHM_CIRCULAR = 690023;
    const ALGORITHM_CENTRAL = 690024;
    const LABEL_CLASSIC = 690031;
    const LABEL_LIGHT = 690032;

    /**
     * pStock
     */
    const STOCK_MISSING_SERIE = 180001;

    /**
     * pSurface
     */
    const UNKNOWN = 0.123456789;
    const IGNORED = -1;
    const LABEL_POSITION_LEFT = 880001;
    const LABEL_POSITION_RIGHT = 880002;
    const LABEL_POSITION_TOP = 880003;
    const LABEL_POSITION_BOTTOM = 880004;

}

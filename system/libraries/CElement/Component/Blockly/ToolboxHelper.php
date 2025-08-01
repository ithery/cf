<?php

use CElement_Component_Blockly_BlockHelper as BlockHelper;
use CElement_Component_Blockly_CategoryHelper as CategoryHelper;

class CElement_Component_Blockly_ToolboxHelper {
    public static function getAllCategoryData() {
        $cats = [];
        //LOGIC
        $cats[CategoryHelper::CATEGORY_LOGIC] = [];
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::CONTROLS_IF;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_COMPARE;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_OPERATION;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_NEGATE;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_BOOLEAN;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_NULL;
        $cats[CategoryHelper::CATEGORY_LOGIC][] = BlockHelper::LOGIC_TERNARY;
        //LOOPS
        $cats[CategoryHelper::CATEGORY_LOOPS] = [];
        $cats[CategoryHelper::CATEGORY_LOOPS][] = BlockHelper::CONTROLS_REPEAT_EXT;
        $cats[CategoryHelper::CATEGORY_LOOPS][] = BlockHelper::CONTROLS_WHILE_UNTIL;
        $cats[CategoryHelper::CATEGORY_LOOPS][] = BlockHelper::CONTROLS_FOR;
        $cats[CategoryHelper::CATEGORY_LOOPS][] = BlockHelper::CONTROLS_FOR_EACH;
        //MATH
        $cats[CategoryHelper::CATEGORY_MATH] = [];
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_NUMBER;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_ARITHMETIC;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_SINGLE;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_TRIG;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_CONSTANT;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_NUMBER_PROPERTY;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_ROUND;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_ON_LIST;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_MODULO;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_RANDOM_INT;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_RANDOM_FLOAT;
        $cats[CategoryHelper::CATEGORY_MATH][] = BlockHelper::MATH_ATAN2;
        //TEXT
        $cats[CategoryHelper::CATEGORY_TEXTS] = [];
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_JOIN;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_APPEND;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_LENGTH;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_IS_EMPTY;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_INDEX_OF;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_CHAR_AT;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_GET_SUBSTRING;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_CHANGE_CASE;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_TRIM;
        $cats[CategoryHelper::CATEGORY_TEXTS][] = BlockHelper::TEXT_PROMPT_EXT;
        //LISTS
        $cats[CategoryHelper::CATEGORY_LISTS] = [];
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_CREATE_WITH;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_REPEAT;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_IS_EMPTY;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_INDEX_OF;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_GET_INDEX;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_SET_INDEX;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_GET_SUBLIST;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_SPLIT;
        $cats[CategoryHelper::CATEGORY_LISTS][] = BlockHelper::LISTS_SORT;

        return $cats;
    }
}

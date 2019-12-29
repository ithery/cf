<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class CElement_Component_Blockly_BlockHelper {

    //LOGIC
    const CONTROLS_IF = 'controls_if';
    const LOGIC_COMPARE = 'logic_compare';
    const LOGIC_OPERATION = 'logic_operation';
    const LOGIC_NEGATE = 'logic_negate';
    const LOGIC_BOOLEAN = 'logic_boolean';
    const LOGIC_NULL = 'logic_null';
    const LOGIC_TERNARY = 'logic_ternary';
    //LOOPS
    const CONTROLS_REPEAT_EXT = 'controls_repeat_ext';
    const CONTROLS_WHILE_UNTIL = 'controls_whileUntil';
    const CONTROLS_FOR = 'controls_for';
    const CONTROLS_FOR_EACH = 'controls_forEach';
    const CONTROLS_FLOW_STATEMENTS = 'controls_flow_statements';
    //MATH
    const MATH_NUMBER = 'math_number';
    const MATH_ARITHMETIC = 'math_arithmetic';
    const MATH_SINGLE = 'math_single';
    const MATH_TRIG = 'math_trig';
    const MATH_CONSTANT = 'math_constant';
    const MATH_NUMBER_PROPERTY = 'math_number_property';
    const MATH_ROUND = 'math_round';
    const MATH_ON_LIST = 'math_on_list';
    const MATH_MODULO = 'math_modulo';
    const MATH_CONSTRAIN = 'math_constrain';
    const MATH_RANDOM_INT = 'math_random_int';
    const MATH_RANDOM_FLOAT = 'math_random_float';
    const MATH_ATAN2 = 'math_atan2';
    //TEXT
    const TEXT = 'text';
    const TEXT_JOIN = 'text_join';
    const TEXT_APPEND = 'text_append';
    const TEXT_LENGTH = 'text_length';
    const TEXT_IS_EMPTY = 'text_isEmpty';
    const TEXT_INDEX_OF = 'text_indexOf';
    const TEXT_CHAR_AT = 'text_charAt';
    const TEXT_GET_SUBSTRING = 'text_getSubstring';
    const TEXT_CHANGE_CASE = 'text_changeCase';
    const TEXT_TRIM = 'text_trim';
    const TEXT_PRINT = 'text_print';
    const TEXT_PROMPT_EXT = 'text_prompt_ext';
    //LIST
    const LISTS_CREATE_WITH = 'lists_create_with';
    const LISTS_REPEAT = 'lists_repeat';
    const LISTS_LENGTH = 'lists_length';
    const LISTS_IS_EMPTY = 'lists_isEmpty';
    const LISTS_INDEX_OF = 'lists_indexOf';
    const LISTS_GET_INDEX = 'lists_getIndex';
    const LISTS_SET_INDEX = 'lists_setIndex';
    const LISTS_GET_SUBLIST = 'lists_getSublist';
    const LISTS_SPLIT = 'lists_split';
    const LISTS_SORT = 'lists_sort';
    //COLOUR
    const COLOUR_PICKER = 'colour_picker';
    const COLOUR_RANDOM = 'colour_random';
    const COLOUR_RGB = 'colour_rgb';
    const COLOUR_BLEND = 'colour_blend';

    public static function renderBlock($blockName) {
        $renderFunctionName = 'render' . carr::reduce(explode('_', $blockName), function($output, $value) {
                    return $output.ucfirst(cstr::camel($value));
                }, '');
        if (method_exists(static::class, $method_name)) {
            return static::$renderFunctionName();
        }
        throw new Exception('No function ' . $renderFunctionName . ' is defined in BlockHelper');
    }

    public function renderControlsIf() {
        return '<block type="controls_if"></block>';
    }

    public function renderLogicCompare() {
        return '<block type="logic_compare"></block>';
    }

    public function renderLogicOperation() {
        return '<block type="logic_operation"></block>';
    }

    public function renderLogicNegate() {
        return '<block type="logic_negate"></block>';
    }

    public function renderLogicBoolean() {
        return '<block type="logic_boolean"></block>';
    }

    public function renderLogicNull() {
        return '<block type="logic_null"></block>';
    }

    public function renderControlsRepeatExt($num = 10) {
        return '<block type="controls_repeat_ext"><value name="TIMES"><shadow type="math_number"><field name="NUM">' . $num . '</field></shadow></value></block>';
    }

}

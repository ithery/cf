<?php

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

    /**
     * @param string $blockName
     *
     * @throws Exception
     *
     * @return string
     */
    public static function renderBlock($blockName) {
        $renderFunctionName = 'render' . carr::reduce(explode('_', $blockName), function ($output, $value) {
            return $output . ucfirst(cstr::camel($value));
        }, '');
        if (method_exists(static::class, $renderFunctionName)) {
            return static::$renderFunctionName();
        }

        throw new Exception('No function ' . $renderFunctionName . ' is defined in BlockHelper');
    }

    /**
     * @return string
     */
    public static function renderControlsIf() {
        return '<block type="controls_if"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicCompare() {
        return '<block type="logic_compare"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicOperation() {
        return '<block type="logic_operation"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicNegate() {
        return '<block type="logic_negate"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicBoolean() {
        return '<block type="logic_boolean"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicNull() {
        return '<block type="logic_null"></block>';
    }

    /**
     * @return string
     */
    public static function renderLogicTernary() {
        return '<block type="logic_ternary"></block>';
    }

    /**
     * @param int $num
     *
     * @return string
     */
    public static function renderControlsRepeatExt($num = 10) {
        return '<block type="controls_repeat_ext"><value name="TIMES"><shadow type="math_number"><field name="NUM">' . $num . '</field></shadow></value></block>';
    }

    /**
     * @return string
     */
    public static function renderControlsWhileUntil() {
        return '<block type="controls_whileUntil"></block>';
    }

    /**
     * @param int $from
     * @param int $to
     * @param int $by
     *
     * @return string
     */
    public static function renderControlsFor($from = 1, $to = 10, $by = 1) {
        return '<block type="controls_for">
        <value name="FROM">
          <shadow type="math_number">
            <field name="NUM">' . $from . '</field>
          </shadow>
        </value>
        <value name="TO">
          <shadow type="math_number">
            <field name="NUM">' . $to . '</field>
          </shadow>
        </value>
        <value name="BY">
          <shadow type="math_number">
            <field name="NUM">' . $by . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderControlsForEach() {
        return '<block type="controls_forEach"></block>';
    }

    /**
     * @return string
     */
    public static function renderControlsFlowStatements() {
        return '<block type="controls_flow_statements"></block>';
    }

    /**
     * @param int $number
     *
     * @return string
     */
    public static function renderMathNumber($number = 123) {
        return '<block type="math_number">
        <field name="NUM">' . $number . '</field>
      </block>';
    }

    /**
     * @param int $a
     * @param int $b
     *
     * @return string
     */
    public static function renderMathArithmetic($a = 1, $b = 1) {
        return '<block type="math_arithmetic">
        <value name="A">
          <shadow type="math_number">
            <field name="NUM">' . $a . '</field>
          </shadow>
        </value>
        <value name="B">
          <shadow type="math_number">
            <field name="NUM">' . $b . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param int $number
     *
     * @return string
     */
    public static function renderMathSingle($number = 9) {
        return '<block type="math_single">
        <value name="NUM">
          <shadow type="math_number">
            <field name="NUM">' . $number . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param int $number
     *
     * @return string
     */
    public static function renderMathTrig($number = 45) {
        return '<block type="math_trig">
        <value name="NUM">
          <shadow type="math_number">
            <field name="NUM">' . $number . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderMathConstant() {
        return '<block type="math_constant"></block>';
    }

    /**
     * @param int $numberToCheck
     *
     * @return string
     */
    public static function renderMathNumberProperty($numberToCheck = 0) {
        return '<block type="math_number_property">
        <value name="NUMBER_TO_CHECK">
          <shadow type="math_number">
            <field name="NUM">' . $numberToCheck . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param float $num
     *
     * @return string
     */
    public static function renderMathRound($num = 3.1) {
        return '<block type="math_number_property">
        <value name="NUMBER_TO_CHECK">
          <shadow type="math_number">
            <field name="NUM">' . $num . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderMathOnList() {
        return '<block type="math_on_list"></block>';
    }

    /**
     * @param int $dividend
     * @param int $divisor
     *
     * @return string
     */
    public static function renderMathModulo($dividend = 64, $divisor = 10) {
        return '<block type="math_modulo">
        <value name="DIVIDEND">
          <shadow type="math_number">
            <field name="NUM">' . $dividend . '</field>
          </shadow>
        </value>
        <value name="DIVISOR">
          <shadow type="math_number">
            <field name="NUM">' . $divisor . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param int $value
     * @param int $low
     * @param int $high
     *
     * @return string
     */
    public static function renderMathConstrain($value = 50, $low = 1, $high = 100) {
        return '<block type="math_constrain">
        <value name="VALUE">
          <shadow type="math_number">
            <field name="NUM">' . $value . '</field>
          </shadow>
        </value>
        <value name="LOW">
          <shadow type="math_number">
            <field name="NUM">' . $low . '</field>
          </shadow>
        </value>
        <value name="HIGH">
          <shadow type="math_number">
            <field name="NUM">' . $high . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param int $from
     * @param int $to
     *
     * @return string
     */
    public static function renderMathRandomInt($from = 1, $to = 100) {
        return '<block type="math_random_int">
        <value name="FROM">
          <shadow type="math_number">
            <field name="NUM">' . $from . '</field>
          </shadow>
        </value>
        <value name="TO">
          <shadow type="math_number">
            <field name="NUM">' . $to . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderMathRandomFloat() {
        return '<block type="math_random_float"></block>';
    }

    /**
     * @param int $x
     * @param int $y
     *
     * @return string
     */
    public static function renderMathAtan2($x = 1, $y = 1) {
        return '<block type="math_atan2">
        <value name="X">
          <shadow type="math_number">
            <field name="NUM">' . $x . '</field>
          </shadow>
        </value>
        <value name="Y">
          <shadow type="math_number">
            <field name="NUM">' . $y . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderText() {
        return '<block type="text"></block>';
    }

    /**
     * @return string
     */
    public static function renderTextJoin() {
        return '<block type="text_join"></block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextAppend($text = '') {
        return '<block type="text_append">
        <value name="TEXT">
          <shadow type="text">' . $text . '</shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextLength($text = 'abc') {
        return ' <block type="text_length">
        <value name="VALUE">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextIsEmpty($text = '') {
        return '<block type="text_isEmpty">
        <value name="VALUE">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextIndexOf($text = 'abc') {
        return '<block type="text_indexOf">
        <value name="VALUE">
          <block type="variables_get">
            <field name="VAR">{textVariable}</field>
          </block>
        </value>
        <value name="FIND">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderTextCharAt() {
        return '<block type="text_charAt">
        <value name="VALUE">
          <block type="variables_get">
            <field name="VAR">{textVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderTextGetSubstring() {
        return '<block type="text_getSubstring">
        <value name="STRING">
          <block type="variables_get">
            <field name="VAR">{textVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderTextChangeCase() {
        return '<block type="text_changeCase">
        <value name="TEXT">
          <shadow type="text">
            <field name="TEXT">abc</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextTrim($text = 'abc') {
        return '<block type="text_trim">
        <value name="TEXT">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextPrint($text = 'abc') {
        return '<block type="text_print">
        <value name="TEXT">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public static function renderTextPromptExt($text = 'abc') {
        return '<block type="text_prompt_ext">
        <value name="TEXT">
          <shadow type="text">
            <field name="TEXT">' . $text . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsCreateWith() {
        return '<block type="lists_create_with">
        <mutation items="0"></mutation>
      </block>';
    }

    /**
     * @param int $num
     *
     * @return string
     */
    public static function renderListsRepeat($num = 5) {
        return '<block type="lists_repeat">
        <value name="NUM">
          <shadow type="math_number">
            <field name="NUM">' . $num . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsLength() {
        return '<block type="lists_length"></block>';
    }

    /**
     * @return string
     */
    public static function renderListsIsEmpty() {
        return '<block type="lists_isEmpty"></block>';
    }

    /**
     * @return string
     */
    public static function renderListsIndexOf() {
        return '<block type="lists_indexOf">
        <value name="VALUE">
          <block type="variables_get">
            <field name="VAR">{listVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsGetIndex() {
        return '<block type="lists_getIndex">
        <value name="VALUE">
          <block type="variables_get">
            <field name="VAR">{listVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsSetIndex() {
        return '<block type="lists_setIndex">
        <value name="LIST">
          <block type="variables_get">
            <field name="VAR">{listVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsGetSublist() {
        return '<block type="lists_getSublist">
        <value name="LIST">
          <block type="variables_get">
            <field name="VAR">{listVariable}</field>
          </block>
        </value>
      </block>';
    }

    /**
     * @param string $delimiter
     *
     * @return string
     */
    public static function renderListsSplit($delimiter = ',') {
        return '<block type="lists_split">
        <value name="DELIM">
          <shadow type="text">
            <field name="TEXT">' . $delimiter . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @return string
     */
    public static function renderListsSort() {
        return '<block type="lists_sort"></block>';
    }

    /**
     * @return string
     */
    public static function renderColourPicker() {
        return '<block type="colour_picker"></block>';
    }

    /**
     * @return string
     */
    public static function renderColourRandom() {
        return '<block type="colour_random"></block>';
    }

    /**
     * @param int $red
     * @param int $green
     * @param int $blue
     *
     * @return string
     */
    public static function renderColourRgb($red = 100, $green = 50, $blue = 0) {
        return '<block type="colour_rgb">
        <value name="RED">
          <shadow type="math_number">
            <field name="NUM">' . $red . '</field>
          </shadow>
        </value>
        <value name="GREEN">
          <shadow type="math_number">
            <field name="NUM">' . $green . '</field>
          </shadow>
        </value>
        <value name="BLUE">
          <shadow type="math_number">
            <field name="NUM">' . $blue . '</field>
          </shadow>
        </value>
      </block>';
    }

    /**
     * @param string $colour1
     * @param string $colour2
     * @param float $ratio
     *
     * @return string
     */
    public static function renderColourBlend($colour1 = '#ff0000', $colour2 = '#3333ff', $ratio = 0.5) {
        return '<block type="colour_blend">
        <value name="COLOUR1">
          <shadow type="colour_picker">
            <field name="COLOUR">' . $colour1 . '</field>
          </shadow>
        </value>
        <value name="COLOUR2">
          <shadow type="colour_picker">
            <field name="COLOUR">' . $colour2 . '</field>
          </shadow>
        </value>
        <value name="RATIO">
          <shadow type="math_number">
            <field name="NUM">' . $ratio . '</field>
          </shadow>
        </value>
      </block>';
    }
}

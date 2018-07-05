<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 4:27:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */

/**
 * SCSS compressed formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class CClientScript_Compiler_Css_Scss_Formatter_Compressed extends CClientScript_Compiler_Css_Scss_Formatter {

    public $open = "{";
    public $tagSeparator = ",";
    public $assignSeparator = ":";
    public $break = "";

    public function indentStr($n = 0) {
        return "";
    }

}

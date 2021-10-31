<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 22, 2018, 4:27:10 PM
 */

/**
 * SCSS compressed formatter
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class CClientScript_Compiler_Css_Scss_Formatter_Compressed extends CClientScript_Compiler_Css_Scss_Formatter {
    public $open = '{';

    public $tagSeparator = ',';

    public $assignSeparator = ':';

    public $break = '';

    public function indentStr($n = 0) {
        return '';
    }
}

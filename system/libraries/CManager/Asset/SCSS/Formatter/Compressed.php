<?php


/**
 * SCSS compressed formatter.
 *
 * @author Leaf Corcoran <leafot@gmail.com>
 */
class CManager_Asset_SCSS_Formatter_Compressed extends CManager_Asset_SCSS_Formatter {
    public $open = '{';

    public $tagSeparator = ',';

    public $assignSeparator = ':';

    public $break = '';

    public function indentStr($n = 0) {
        return '';
    }
}

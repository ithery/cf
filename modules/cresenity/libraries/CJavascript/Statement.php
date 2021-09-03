<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 8:27:19 PM
 */
abstract class CJavascript_Statement implements CJavascript_StatementInterface {
    public function hash() {
        return spl_object_hash($this);
    }
}

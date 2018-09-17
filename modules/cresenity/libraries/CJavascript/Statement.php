<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 8:27:19 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CJavascript_Statement implements CJavascript_StatementInterface {

    public function hash() {
        return spl_object_hash($this);
    }

}

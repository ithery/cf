<?php

defined('SYSPATH') or die('No direct access allowed.');

abstract class CJavascript_Statement implements CJavascript_StatementInterface {
    public function hash() {
        return spl_object_hash($this);
    }
}

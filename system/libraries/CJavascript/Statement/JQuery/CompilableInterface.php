<?php

defined('SYSPATH') or die('No direct access allowed.');

interface CJavascript_Statement_JQuery_CompilableInterface {
    /**
     * @return string;
     */
    public function compile();
}

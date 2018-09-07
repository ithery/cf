<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Sep 2, 2018, 9:42:39 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CJavascript_Statement_JQuery_CompilableInterface {

    /**
     * @return string;
     */
    public function compile();
}

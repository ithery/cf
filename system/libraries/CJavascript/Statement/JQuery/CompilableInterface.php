<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Sep 2, 2018, 9:42:39 PM
 */
interface CJavascript_Statement_JQuery_CompilableInterface {
    /**
     * @return string;
     */
    public function compile();
}

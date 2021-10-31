<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 5:19:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
interface CClientScript_Compiler_Engine {

    public function compile($code, $name = null);

    public static function getVersion();
}

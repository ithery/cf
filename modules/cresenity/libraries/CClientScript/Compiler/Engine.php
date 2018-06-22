<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 22, 2018, 5:19:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
abstract class CClientScript_Compiler_Engine {
    static public $VERSION = 'v0.0.1';
    
    public static function getVersion() {
        return self::VERSION;
    }

}

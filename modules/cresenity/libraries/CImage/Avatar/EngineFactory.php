<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Feb 16, 2019, 2:03:00 AM
 */
class CImage_Avatar_EngineFactory {
    public static function create($engineName) {
        $className = 'CImage_Avatar_Engine_' . $engineName;
        return new $className();
    }
}

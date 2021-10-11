<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Aug 22, 2018, 11:59:51 PM
 */
class CSerializer {
    public static function json() {
        return new CSerializer_JsonSerializer();
    }
}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Aug 22, 2018, 11:59:51 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CSerializer {

    public static function json() {
        return new CSerializer_JsonSerializer();
    }

}

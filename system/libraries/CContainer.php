<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 1:17:10 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CContainer {

    public static function createContainer() {
        return new CContainer_Container();
    }

}

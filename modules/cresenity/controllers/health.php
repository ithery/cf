<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @since Sep 24, 2020 
 * @license Ittron Global Teknologi
 */
class Controller_Health extends CController {

    public function check() {
        echo 'OK';
    }

}

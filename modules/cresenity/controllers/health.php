<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan <hery@itton.co.id>
 * @license Ittron Global Teknologi
 *
 * @since Sep 24, 2020
 */
class Controller_Health extends CController {
    public function check() {
        echo 'OK';
    }
}

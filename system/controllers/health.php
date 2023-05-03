<?php

defined('SYSPATH') or die('No direct access allowed.');

class Controller_Health extends CController {
    public function check() {
        return c::response('OK');
    }
}

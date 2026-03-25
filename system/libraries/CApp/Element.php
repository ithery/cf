<?php

defined('SYSPATH') or die('No direct access allowed.');

class CApp_Element extends CObservable {
    public function __construct($id = '') {
        parent::__construct($id);
    }
}

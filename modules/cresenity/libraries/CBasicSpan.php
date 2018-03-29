<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CBasicSpan extends CElement_Element_Span {

    public static function factory($id = "") {
        return new CBasicSpan($id);
    }

}

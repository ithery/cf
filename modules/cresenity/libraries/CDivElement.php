<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CDivElement extends CElement_Element_Div {

    
    public static function factory($id = "") {
        return new CDivElement($id);
    }
    

}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Br extends CMobile_Element {


	protected $src = "";

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "br";
		$this->is_empty = true;
		$this->src = "";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Br($id);
    }

	
	

}

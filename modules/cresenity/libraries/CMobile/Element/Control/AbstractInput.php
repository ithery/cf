<?php

abstract class CMobile_Element_Control_AbstractInput extends CMobile_Element_AbstractControl {


    protected $type;
    
    public function __construct($id = "") {

        parent::__construct($id);

        $this->type = "text";
        $this->tag = "input";
        $this->name = $id;

        //sanitize name
        $this->id = str_replace("[", "", $this->id);
        $this->id = str_replace("]", "", $this->id);

        $this->submit_onchange = false;
        
    }

    

	
	protected function html_attr() {
		$html_attr = parent::html_attr();

		
		$value = ' value="'.$this->value.'"';
		
		$html_attr = $value.$html_attr;
		return $html_attr;
	}

    }

?>
<?php

abstract class CMobile_Element_Control_AbstractInput extends CMobile_Element_AbstractControl {

    protected $type;
    protected $length;
    
    public function __construct($id = "") {

        parent::__construct($id);

        $this->type = "text";
        $this->tag = "input";
        $this->name = $id;
        $this->length = '';

        //sanitize name
        $this->id = str_replace("[", "", $this->id);
        $this->id = str_replace("]", "", $this->id);

        $this->submit_onchange = false;
        
    }

    public function set_length($length) {
        $this->length = $length;
        return $this;
    }

	protected function html_attr() {
		$html_attr = parent::html_attr();
        $length = "";
         if (strlen($this->length) > 0)
            $length = ' length="'.$this->length.'"';
		$value = ' value="'.$this->value.'"';
		
		$html_attr = $value.$html_attr.$length;
		return $html_attr;
	}

}

?>
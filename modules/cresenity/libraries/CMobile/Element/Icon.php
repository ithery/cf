<?php

class CMobile_Element_Icon extends CMobile_Element {

    protected $icon;
    protected $type;
    public function __construct($id) {
        parent::__construct($id);
        $this->icon = "";
		$this->tag = "i";
		$this->type = "left";
    }

    public static function factory($id = '') {
        return new CMobile_Element_Icon($id);
    }
	
	public function set_type($type) {
        $this->type = $type;
        return $this;
		
	}

    public function set_icon($ic) {
        $this->icon = $ic;
        return $this;
    }

    protected function html_attr() {
			
		$this->add_class('material-icons');
		$this->add_class($this->type);
		
		return parent::html_attr();
		
		
	}
		
	public function html($indent = 0) {
		$this->add($this->icon);
		return parent::html($indent);
		
	}

    

}

?>
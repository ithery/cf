<?php

    abstract class CMobile_Element_AbstractControl extends CMobile_Element {

        protected $transforms = array();
        protected $name;
        
        protected $submit_onchange;
        protected $value;
        protected $size;
        protected $ajax;

        protected $validation;
        protected $disabled;
        protected $readonly;

        public function __construct($id = "") {

            parent::__construct($id);

            
            $this->tag = "input";
            $this->name = $id;

            //sanitize name
            $this->id = str_replace("[", "", $this->id);
            $this->id = str_replace("]", "", $this->id);

            $this->submit_onchange = false;
            $this->ajax = false;
            $this->size = "medium";
            $this->value = "";
            $this->disabled = "";
   
            $this->validation = CFormValidation::factory();
        }

        

        public function set_submit_onchange($bool) {
            $this->submit_onchange = $bool;
            return $this;
        }

        public function set_ajax($bool) {
            $this->ajax = $bool;
            return $this;
        }

        public function set_disabled($bool) {
            $this->disabled = $bool;
            return $this;
        }

        public function set_size($size) {
            $this->size = $size;
            return $this;
        }

        public function set_readonly($bool) {
            $this->readonly = $bool;
            return $this;
        }

       
        public function add_transform($name, $args = array()) {
            $func = CDynFunction::factory($name);
            if (!is_array($args)) {
                $args = array($args);
            }
            foreach ($args as $arg) {
                $func->add_param($arg);
            }


            $this->transforms[] = $func;
            return $this;
        }

        public function set_value($val) {
            $this->value = $val;
            return $this;
        }

        public function set_name($val) {
            $this->name = $val;
            return $this;
        }

        public function add_validation($name, $value = "") {
            if (strlen($value) == 0) $value = $name;
            $this->validation->add_validation($name, $value);
            return $this;
        }


        protected function html_attr() {
			$html_attr = parent::html_attr();
			$disabled = "";
			$readonly = "";
			if ($this->disabled)
				$disabled = ' disabled="disabled"';
			
			if ($this->readonly)
				$readonly = ' readonly="readonly"';
			
			
			$name = ' name="'.$this->name.'"';
			
			
			
			$html_attr = $name.$html_attr.$disabled.$readonly;
			return $html_attr;
		}

        public function js($indent = 0) {
            $js = "";
            if ($this->submit_onchange) {
                
                $js.="
					$('#" . $this->id . "').on('change',function() {
						$(this).closest('form').submit();
					});
				
				";
            }
            $js.= parent::js($indent);
            return $js;
        }


    
    }

?>
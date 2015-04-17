<?php

    class CFormInput extends CElement {

        protected $transforms = array();
        protected $name;
        protected $type;
        protected $submit_onchange;
        protected $value;
        protected $size;
        protected $ajax;
        protected $list;
        protected $validation;
        protected $disabled;
        protected $readonly;

        public function __construct($id = "") {

            parent::__construct($id);

            $this->type = "text";
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
            $this->list = array();
            $this->validation = CFormValidation::factory();
        }

        public static function factory($id = "") {
            return new CFormInput($id);
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

        public function get_field_id() {
            return $this->field_id;
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

        public function set_list($list) {
            $this->list = $list;
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

        public function set_type($type) {
            $this->type = $type;
            return $this;
        }

        public function set_on_text($text) {
            $this->on_text = $text;
            return $this;
        }

        public function set_off_text($text) {
            $this->off_text = $text;
            return $this;
        }

        public function set_checked($bool) {
            $this->checked = $bool;
            return $this;
        }

        public function show_updown() {
            $this->showupdown = true;
            return $this;
        }

        public function hide_updown() {
            $this->showupdown = false;
            return $this;
        }

        public function toarray() {
            $data = array();
            if ($this->disabled) {
                $data['attr']['disabled'] = "disabled";
            }
            if ($this->readonly) {
                $data['attr']['readonly'] = "readonly";
            }
            if (strlen($this->name) > 0) {
                $data['attr']['name'] = $this->name;
            }
            $data = array_merge_recursive($data, parent::toarray());
            return $data;
        }

        public function html($indent = 0) {
            return parent::html($indent);
        }

        public function js($indent = 0) {
            $js = "";
            if ($this->submit_onchange) {
                if ($this->type == "date") {
                    $js.="
						$('#" . $this->id . "').on('changeDate',function() {
							$(this).closest('form').submit();
						});
					
					";
                }
                $js.="
					$('#" . $this->id . "').on('change',function() {
						$(this).closest('form').submit();
					});
				
				";
            }
            $js.= parent::js($indent);
            return $js;
        }

        /**
         * @return self
         */
//        abstract function set_placeholder();
//        abstract function set_query();
    
    }

?>
<?php

    class CFormInputTag extends CFormInput {

      

		
        public function __construct($id) {
            parent::__construct($id);

         
          
            
			
        }

        public static function factory($id) {
            return new CFormInputTag($id);
        }

        public function set_multiple($bool) {
            $this->multiple = $bool;
            return $this;
        }

        public function set_min_input_length($min_input_length) {
            $this->min_input_length = $min_input_length;
            return $this;
        }

        public function set_key_field($key_field) {
            $this->key_field = $key_field;
            return $this;
        }

        public function set_search_field($search_field) {
            $this->search_field = $search_field;
            return $this;
        }

        public function set_query($query) {
            $this->query = $query;
            return $this;
        }

        public function set_format_result($fmt) {
            $this->format_result = $fmt;
            return $this;
        }

        public function set_format_selection($fmt) {
            $this->format_selection = $fmt;
            return $this;
        }

        public function set_placeholder($placeholder) {
            $this->placeholder = $placeholder;
            return $this;
        }

        public function html($indent = 0) {
			
            $html = new CStringBuilder();
			$html->set_indent($indent);
			$html->append(parent::html($indent));
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            $multiple = ' multiple="multiple"';
            if (strlen($custom_css) > 0) {
                $custom_css = ' style="' . $custom_css . '"';
            }

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) $classes = " " . $classes;
            if ($this->bootstrap == '3') {
                $classes = $classes ." form-control ";
            }

            $html->appendln('<select  class="' . $classes . '" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '" ' . $custom_css . $multiple . '><option>A</option><option>B</option></select>')->br();
			
            return $html->text();
        }

       
        public function js($indent = 0) {
           

         
            $str_js_init = "";
            if (strlen($this->value) > 0) {

                $db = CDatabase::instance();
                $rjson = 'false';

                $q = "select * from (" . $this->query . ") as a where `" . $this->key_field . "`=" . $db->escape($this->value);
                $r = $db->query($q)->result_array(false);
                if (count($r) > 0) $r = $r[0];
                $rjson = json_encode($r);


                $str_js_init = "
				initSelection : function (element, callback) {
					
				var data = " . $rjson . "
				
				callback(data);
			},
			";
            }
			$str_multiple = " multiple:'true',";
			$classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0)
                $classes = " " . $classes;
            $str = "
			$('#" . $this->id . "').select2({
				tag: true,
				
			}).change(function() {
				
			});
	
	";

            $js = new CStringBuilder();
            $js->append(parent::js($indent))->br();
            $js->set_indent($indent);
            //echo $str;
            $js->append($str)->br();





            return $js->text();
        }

    }
    
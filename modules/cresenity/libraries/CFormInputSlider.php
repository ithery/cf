<?php

    class CFormInputSlider extends CFormInput {

        protected $min_value;
        protected $max_value;
        protected $value;
        protected $step;
        protected $orientation;
        protected $tooltip;
        
        protected $on_slide;
        protected $on_slide_start;
        protected $on_slide_stop;

        public function __construct($id = "") {
            parent::__construct($id);
            
            $this->min_value = 0;
            $this->max_value = 10;
            $this->value = 0;
            $this->step = 1;
            $this->orientation = 'horizontal';
            $this->tooltip = 'show';
        }

        public static function factory($id = "") {
            return new CFormInputSlider($id);
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) {
                $classes = " " . $classes;
            }

            $html->append('<input type="text" id="' . $this->id . '" name="' . $this->name . '" '
                    . ' class="' .$classes .'" />');

            $html->appendln(parent::html($indent));
            return $html->text();
        }

        public function js($indent = 0) {
            $js = new CStringBuilder();
            
            $js->append("jQuery('#" .$this->id ."').slider({");
            $js->append("   'min': " .$this->min_value .",");
            $js->append("   'max': " .$this->max_value .",");
            $js->append("   'value': " .$this->value .",");
            $js->append("   'step': " .$this->step .",");
            $js->append("   'orientation': '" .$this->orientation ."',");
            $js->append("   'tooltip': '" .$this->tooltip ."',");
            $js->append("})");
            
            if (strlen($this->on_slide) > 0) {
                $js->append(".on('slide', function(e) {");
                $js->append($this->on_slide);
                $js->append("})");
            }
            if (strlen($this->on_slide_stop) > 0) {
                $js->append(".on('slideStop', function(e) {");
                $js->append($this->on_slide_stop);
                $js->append("})");
            }
            $js->append(";");
            
            $js->appendln(parent::js($indent));
            return $js->text();
        }

        public function set_min_value($min_value){
            $this->min_value = $min_value;
            return $this;
        }
        public function set_max_value($max_value){
            $this->max_value = $max_value;
            return $this;
        }
        public function set_value($value){
            $this->value = $value;
            return $this;
        }
    }

?>
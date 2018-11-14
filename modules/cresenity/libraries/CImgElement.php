<?php

    defined('SYSPATH') OR die('No direct access allowed.');

    class CImgElement extends CElement {

        protected $src;

        public function __construct($id = "") {

            parent::__construct($id);
            $this->tag = "img";
            $this->src = curl::base() . "ccore/noimage/40/40";
        }

        public static function factory($id = "") {
            return new CImgElement($id);
        }

        public function set_src($src) {
            $this->src = $src;
            return $this;
        }

        public function setSrc($src) {
            $this->src = $src;
            return $this;
        }

        public function toarray() {
            $data["attr"]["src"] = $this->src;
            $data = array_merge_recursive($data, parent::toarray());
            return $data;
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $disabled = "";
            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if ( strlen($classes) > 0 ) $classes = " " . $classes;
            $custom_css = $this->custom_css;
            $custom_css = crenderer::render_style($custom_css);
            
            $addition_attribute = "";
            foreach($this->attr as $k=>$v) {
                $addition_attribute.=" ".$k.'="'.$v.'"';
            }
            if ( strlen($custom_css) > 0 ) {
                $custom_css = ' style="' . $custom_css . '"';
            }
            $html->appendln('<img id="' . $this->id . '" src="' . $this->src . '" class="' . $classes . '"' . $custom_css . $addition_attribute .'>');
            return $html->text();
        }

        public function js($indent = 0) {
            return parent::js($indent);
        }

    }
    
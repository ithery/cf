<?php

    class CCollapse extends CElement {

        protected $label;
        protected $active;
        protected $icon;
        protected $target;
        protected $ajax_url;
        protected $ajax;

        public function __construct($id = "") {
            parent::__construct($id);
            $this->label = "";
            $this->target = "";
            $this->icon = "";
            $this->ajax_url = "";
            $this->ajax = true;
            $this->active = false;
        }

        public static function factory($id = "") {
            return new CCollapse($id);
        }

        public function set_active($bool) {
            $this->active = $bool;
            return $this;
        }

        public function set_nopadding($bool) {
            $this->nopadding = $bool;
            return $this;
        }

        public function set_label($label, $lang = true) {
            if ($lang) $label = clang::__($label);
            $this->label = $label;
            return $this;
        }

        public function set_icon($icon) {
            $this->icon = $icon;
            return $this;
        }

        public function set_target($target) {
            $this->target = $target;
            return $this;
        }

        public function set_ajax_url($url) {
            $this->ajax_url = $url;
            return $this;
        }

        public function set_ajax($bool) {
            $this->ajax = $bool;
            return $this;
        }

        public function header_html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);

            if (strlen($this->ajax_url) == 0) {
                if ($this->ajax) {
                    $ajax_url = CAjaxMethod::factory()->set_type('reload')
                            ->set_data('json', $this->json())
                            ->makeurl();
                    $this->set_ajax_url($ajax_url);
                }
            }
            
            $data_url = '';
            if (strlen($this->ajax_url) > 0) {
                $data_url = ' data-url="' . $this->ajax_url . '"';
            }
            
            $class_active = '';
            if ($this->active) {
                $class_active = " active ";
            }
            $html->appendln('<li class="' .$class_active .'">');
            $html->appendln('   <a href="javascript;" data-target="'.$this->id .'" ' .$data_url .'>'.$this->label .'</a>');
            $html->appendln('</li>');
            return $html->text();
        }

        public function html($indent = 0) {
            $html = new CStringBuilder();
            $html->set_indent($indent);
            $add_class = "";
            $class_active = "";
            if ($this->active) {
                $class_active = " active ";
            }
            $data_url = '';
            if (strlen($this->ajax_url) > 0) {
                $data_url = ' data-url="' . $this->ajax_url . '"';
            }

            $html->appendln('<div id="' .$this->id .'" class="collapse-panel">');
            $html->appendln('   <div class="collapse-heading hidden-lg hidden-md" ' .$data_url .'>');
            $html->appendln('   ' . $this->label);
            $html->appendln('   </div>');
            $html->appendln('   <div id="body-' .$this->id .'" class="collapse-body ' . $class_active . '">');
            $html->appendln('   ' . parent::html($html->get_indent()));
            $html->appendln('   </div>');
            $html->appendln('</div>');
            return $html->text();
        }

        public function js($indent = 0) {

            $js = new CStringBuilder();
            $js->set_indent($indent);

            $js->appendln(parent::js($indent));


            return $js->text();
        }

    }

?>
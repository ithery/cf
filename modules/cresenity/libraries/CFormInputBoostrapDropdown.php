<?php

    /**
     *
     * @author Raymond Sugiarto
     * @since  Feb 11, 2016
     */
    class CFormInputBoostrapDropdown extends CFormInput {

        private $opt_is_hover;
        private $opt_on_close;
        private $placeholder;
        private $width_dropdown_menu;
        private $height_dropdown_menu;
        private $masking = false;
        public static $instance = null;

        public function __construct($id = "") {
            parent::__construct($id);

            $this->opt_is_hover = true;
            $this->opt_on_close = true;
            $this->placeholder = '';
            $this->width_dropdown_menu = null;
            $this->height_dropdown_menu = null;
        }

        public static function factory($id) {
            return new CFormInputBoostrapDropdown($id);
        }
        
        public static function instance($id){
            if (self::$instance == null) {
                if (!isset(self::$instance[$id])) {
                    self::$instance[$id] = self::factory($id);
                }
            }
            return self::$instance[$id];
        }

        public function html($indent = 0) {
            $html = CStringBuilder::factory();

            $classes = $this->classes;
            $classes = implode(" ", $classes);
            if (strlen($classes) > 0) {
                $classes = " " . $classes;
            }

            $addition_attribute = "";
            foreach ($this->attr as $k => $v) {
                $addition_attribute.=" " . $k . '="' . $v . '"';
            }
            $html->appendln('<div class="btn-group bs-dropdown input-group ' . $classes . '" id="' . $this->id . '-select" ' .$addition_attribute .'>');
            $html->appendln('<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" value="' . $this->value . '"/>');
            $default_value = carr::get($this->list, $this->value);
            if ($default_value instanceof CRenderable) {
                $default_value = $default_value->html();
            }
            if (is_array($default_value)) {
                $default_value = carr::get($default_value, 'mask');
            }
            if ($default_value == null) {
                $default_value = $this->placeholder;
            }
            $html->appendln('<a class="btn btn-default dropdown-toggle selected" data-toggle="dropdown" id="' . $this->id . '-show" href="#">'
                    .'<span id="' . $this->id . '-show-span" class="label-dropdown-toggle">'. $default_value .'</span>');
//            $html->appendln('<span class="glyphicon glyphicon-triangle-bottom pull-right"></span>');
//            $html->appendln('<i class="fa fa-sort-desc"></i>');
            $html->appendln('</a>');

            $dropdown_menu_style = '';
            if ($this->width_dropdown_menu != null) {
                $dropdown_menu_style .= 'width: ' . $this->width_dropdown_menu . 'px;';
            }
            if ($this->height_dropdown_menu != null) {
                $dropdown_menu_style .= 'height: ' . $this->height_dropdown_menu . 'px;';
            }
            $dropdown_menu_style = 'style=" ' . $dropdown_menu_style . '"';

            $html->appendln('   <ul class="dropdown-menu" ' . $dropdown_menu_style . '>');
            foreach ($this->list as $list_k => $list_v) {
                $hover_class = '';
                if ($this->opt_is_hover) {
                    $hover_class = 'hover';
                }
                $link = 'javascript:void(0);';
                if ($list_v instanceof CRenderable) {
                    $list_v = $list_v->html();
                }
                
                if ($this->masking == false) {
                    $html->appendln('
                        <li class="dropdown-menu-list" val="' . $list_k . '">
                            <a class="dropdown-menu-list-link ' . $hover_class . '" href="' .$link .'">' . $list_v . '</a>
                        </li>
                            ');
                }
                else {
                    $mask = $list_v;
                    $value = $list_v;
                    $link = 'javascript:void(0);';
                    if (is_array($list_v)) {
                        $mask = carr::get($list_v, 'mask');
                        $value = carr::get($list_v, 'value');
                        $link = carr::get($list_v, 'link', 'javascript:void(0);');
                    }
                    $html->appendln('
                        <li class="dropdown-menu-list" val="' . $list_k . '">
                            <span class="dropdown-show-value hide">' .$value .'</span>
                            <a class="dropdown-menu-list-link ' . $hover_class . '" href="' .$link .'">' . $mask . '</a>
                        </li>
                            ');
                }
            }
            $html->appendln('   </ul>');
            $html->appendln('</div>');
            $html->appendln(parent::html());

            return $html->text();
        }

        public function js($indent = 0) {
            $js = CStringBuilder::factory();

            if ($this->opt_on_close == false) {
                $js->appendln('
                    jQuery("#' . $this->id . '-select .dropdown-menu").click(function(e){
                        e.stopPropagation();
                    });
                ');
            }
            else {
                $js->appendln('
                        jQuery("#' . $this->id . '-select .dropdown-menu-list").on("click", function(){
                            var value = jQuery(this).attr("val");
                            var show_value = jQuery(this).find(".dropdown-show-value").html();
                            if (typeof show_value === "undefined") {
                                show_value = value;
                            }
                            jQuery("#' .$this->id .'").val(value);
                            jQuery("#' .$this->id .'-show-span").html(show_value);
                        });  
                    ');
            }
            
            foreach ($this->list as $list_k => $list_v) {
                if ($list_v instanceof CRenderable) {
                    $js->appendln($list_v->js());
                }
            }
            $js->appendln(parent::js());
            return $js->text();
        }

        
        public function get_masking() {
            return $this->masking;
        }

        public function set_masking($masking) {
            $this->masking = $masking;
            return $this;
        }

        public function get_list() {
            return $this->list;
        }

        public function set_list($list) {
            $this->list = $list;
            return $this;
        }
        
        public function add_list_control($id = '', $type){
            $control = null;
            if ($this->manager->is_registered_control($type)) {
                $control = $this->manager->create_control($id, $type);
            } else {
                trigger_error('Unknown control type ' . $type);
            }
            $this->list[] = $control;
            return $control;
        }

        public function get_opt_is_hover() {
            return $this->opt_is_hover;
        }

        public function get_opt_on_close() {
            return $this->opt_on_close;
        }

        public function get_placeholder() {
            return $this->placeholder;
        }

        public function set_opt_is_hover($opt_is_hover) {
            $this->opt_is_hover = $opt_is_hover;
            return $this;
        }

        public function set_opt_on_close($opt_on_close) {
            $this->opt_on_close = $opt_on_close;
            return $this;
        }

        public function set_placeholder($placeholder) {
            $this->placeholder = $placeholder;
            return $this;
        }

        public function get_width_dropdown_menu() {
            return $this->width_dropdown_menu;
        }

        public function set_width_dropdown_menu($width_dropdown_menu) {
            $this->width_dropdown_menu = $width_dropdown_menu;
            return $this;
        }

        public function set_height_dropdown_menu($height_dropdown_menu) {
            $this->height_dropdown_menu = $height_dropdown_menu;
            return $this;
        }

    }
    
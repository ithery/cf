<?php

class CAction extends CElement {

    protected $jsfunc;
    protected $icon;
    protected $disabled;
    protected $type;
    protected $link_target;
    protected $link;
    protected $label;
    protected $submit;
    protected $submit_to;
    protected $submit_to_target;
    protected $jsparam;
    protected $confirm;
    protected $style;

    public function __construct($id) {
        parent::__construct($id);

        $this->jsfunc = "";
        $this->type = "jsfunc";
        $this->icon = "";
        $this->link = "";
        $this->jsparam = array();
        $this->link_target = "";
        $this->submit = false;
        $this->submit_to = false;
        $this->submit_to_target = false;
        $this->label = "";
        $this->style = "";
        $this->disabled = false;
        $this->confirm = false;
    }

    public static function factory($id = '') {
        return new CAction($id);
    }

    public function set_icon($ic) {
        $this->icon = $ic;
        return $this;
    }

    public function set_confirm($bool) {
        $this->confirm = $bool;
        return $this;
    }

    public function set_type($type) {
        $this->type = $type;
        return $this;
    }

    public function set_label($label, $lang = true) {
        if ($lang)
            $label = clang::__($label);
        $this->label = $label;
        return $this;
    }

    public function set_jsfunc($jsfunc) {
        $this->jsfunc = $jsfunc;
        return $this;
    }

    public function set_jsparam($jsparam) {
        $this->jsparam = $jsparam;
        return $this;
    }

    public function set_link($link) {
        $this->type = "link";
        $this->link = $link;
        return $this;
    }

    public function set_link_target($link_target) {
        $this->link_target = $link_target;
        return $this;
    }

    public function set_submit($bool) {
        $this->submit = $bool;

        return $this;
    }

    public function set_submit_to($url, $target = "") {
        $this->submit_to = $url;

        if (strlen($target) > 0) {
            $this->submit_to_target = $target;
        }
        return $this;
    }

    public function set_disabled($bool) {
        $this->disabled = $bool;
        return $this;
    }

    public function render_as_input() {
        $render_as_input = false;
        if ($this->submit) {
            $render_as_input = true;
        }
        return $render_as_input;
    }

    public function reassign_confirm() {
        if ($this->confirm) {
            //we check the listener
            if (count($this->listeners) > 0) {
                foreach ($this->listeners as $lis) {
                    $lis->set_confirm(true);
                }
                $this->set_confirm(false);
            }
        }
    }

    public function toarray() {
        $data = array();


        $data = array_merge_recursive($data, parent::toarray());
        if ($this->submit) {
            $data['tag'] = 'input';
            $data['attr']['type'] = 'submit';
            $data['attr']['value'] = $this->label;
            unset($data['text']);
        } else {
            $data['tag'] = 'a';
            $data['attr']['href'] = $this->link;
            $data['text'] = $this->label;
        }

        return $data;
    }

    public function html($indent = 0) {
        $this->reassign_confirm();
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $jsparam = $this->jsparam;
        $link = $this->link;
        $param = "";
        $i = 0;
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute.=" " . $k . '="' . $v . '"';
        }
        foreach ($jsparam as $k => $p) {
            $i++;
            if ($k == "param1") {
                if (strlen($param) > 0)
                    $param .= ",";
                $param .= "'" . $p . "'";
            }
            if ($this->type == "link") {
                //$link = str_replace("{param1}",$p,$link);
                preg_match_all("/{([\w]*)}/", $link, $matches, PREG_SET_ORDER);
                foreach ($matches as $val) {
                    $str = $val[1]; //matches str without bracket {}
                    $b_str = $val[0]; //matches str with bracket {}
                    if ($k == $str) {
                        $link = str_replace($b_str, $p, $link);
                    }
                }
            }
        }
        $link_target = '';
        if (strlen($this->link_target)) {
            $link_target = ' target="' . $this->link_target . '"';
        }
        $disabled = "";
        if ($this->disabled) {
            $disabled = ' disabled="disabled"';
        }
        $add_class = "";
        $add_attr = "";
        if ($this->confirm) {
            $add_class .= " confirm";
        }
        if ($this->style == "btn-icon-group" && strlen($this->label) > 0) {
            $add_class .= " tip-top";
            $add_attr .= ' data-original-title="' . $this->label . '"';
        }
        if ($this->render_as_input()) {
            $input_type = "button";

            if ($this->submit)
                $input_type = "submit";
            $html->appendln('<input id="' . $this->id . '" name="' . $this->id . '" class="btn btn-primary' . $add_class . $classes . '" type="' . $input_type . '" value="' . $this->label . '"' . $disabled . $add_attr .$addition_attribute. $custom_css . '/>');
        } else {
            if ($this->type == "jsfunc") {
                $link = 'javascript:;';
                if ($this->jsfunc != "") {
                    $link = 'javascript:' . $this->jsfunc . '(' . $param . ')';
                }
            }
            if ($this->style == "btn-dropdown") {
                $html->appendln('<li>');
            }
            if ($this->style == "btn-dropdown") {
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $link_target . ' class=" ' . $add_class . '' . $classes . '"' . $disabled . $add_attr .$addition_attribute. $custom_css . '>');
            } else {
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $link_target . ' class="btn ' . $add_class . '' . $classes . '"' . $disabled . $add_attr .$addition_attribute. $custom_css . '>');
            }
            if (strlen($this->icon) > 0) {
                $html->append('<i class="icon icon-' . $this->icon . '"></i> ');
            }
            if ($this->style != "btn-icon-group") {
                $html->append($this->label);
            }
            $html->append('</a>');
            if ($this->style == "btn-dropdown") {
                $html->appendln('</li>');
            }
        }
        return $html->text();
    }

    public function js($indent = 0) {
        $this->reassign_confirm();
        $js = new CStringBuilder();
        $js->set_indent($indent);

        if ($this->disabled) {
            $js->appendln("jQuery('#" . $this->id . "').click(function(e) { e.preventDefault(); });");
        } else {
            if ($this->render_as_input()) {

                if (strlen($this->link) > 0) {
                    if ($this->submit) {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { jQuery(this).closest('form').attr('action','" . $this->link . "'); });");
                    } else {
                        $js->appendln("jQuery('#" . $this->id . "').click(function() { window.location.href='" . $this->link . "'; });");
                    }
                }
            } else {
                if (strlen($this->submit_to) > 0) {
                    $str_submit_to_target = "";
                    if (strlen($this->submit_to_target) > 0) {
                        $str_submit_to_target = "jQuery(this).closest('form').attr('target','" . $this->submit_to_target . "');";
                    }
                    $js->appendln("
						jQuery('#" . $this->id . "').click(function() {
							jQuery(this).closest('form').attr('action','" . $this->submit_to . "');
							" . $str_submit_to_target . "
							jQuery(this).closest('form').submit();
						}
					);");
                }
            }
        }



        $js->appendln(parent::js($js->get_indent()))->br();
        return $js->text();
    }

}

?>
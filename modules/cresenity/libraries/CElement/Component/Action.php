<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since May 12, 2018, 9:08:07 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_Component_Action extends CElement_Component {

    use CTrait_Compat_Element_Action,
        CTrait_Element_Property_Label,
        CTrait_Element_Property_Icon;

    protected $jsfunc;
    protected $disabled;
    protected $type;
    protected $link_target;
    protected $link;
    protected $orig_label;
    protected $submit;
    protected $submitTo;
    protected $submitToTarget;
    protected $jsparam;
    protected $confirm;
    protected $style;
    protected $confirm_message;
    protected $button;
    protected $btn_style;
    protected $value;

    public function __construct($id) {
        parent::__construct($id);

        $this->jsfunc = "";
        $this->type = "jsfunc";
        $this->icon = "";
        $this->link = "";
        $this->jsparam = array();
        $this->link_target = "";
        $this->submit = false;
        $this->submitTo = false;
        $this->submitToTarget = false;
        $this->label = "";
        $this->style = "";
        $this->disabled = false;
        $this->confirm = false;
        $this->confirm_message = "";
        $this->button = false;
        $this->btn_style = 'default';
        $this->value = '';
    }

    public static function factory($id = '') {
        return new CElement_Component_Action($id);
    }

    /**
     * 
     * @param bool $bool
     * @return $this
     */
    public function setConfirm($bool = true) {
        $this->confirm = $bool;
        return $this;
    }

    public function set_confirm_message($message) {
        $this->confirm_message = $message;
        return $this;
    }

    public function set_type($type) {
        $this->type = $type;
        return $this;
    }

    public function set_value($value) {
        $this->value = $value;
        return $this;
    }

    public function set_jsfunc($jsfunc) {
        $this->jsfunc = $jsfunc;
        return $this;
    }

    public function setJsParam($jsparam) {
        $this->jsparam = $jsparam;
        return $this;
    }

    public function setLink($link) {
        $this->type = "link";
        $this->link = $link;
        return $this;
    }

    public function setLinkTarget($linkTarget) {
        $this->link_target = $linkTarget;
        return $this;
    }

    public function setSubmit($bool = true) {
        $this->submit = $bool;
        return $this;
    }

    public function setSubmitTo($url, $target = "") {
        $this->submitTo = $url;

        if (strlen($target) > 0) {
            $this->submitToTarget = $target;
        }
        return $this;
    }

    public function setDisabled($bool = true) {
        $this->disabled = $bool;
        return $this;
    }

    public function set_button($bool) {
        $this->button = $bool;
        return $this;
    }

    public function renderAsInput() {
        $render_as_input = false;
        if ($this->submit) {
            $render_as_input = true;
        }
        return $render_as_input;
    }

    public function reassignConfirm() {
        if ($this->confirm) {
            //we check the listener
            if (count($this->listeners) > 0) {
                foreach ($this->listeners as $lis) {
                    $lis->setConfirm(true)->setConfirmMessage($this->confirm_message);
                }
                $this->setConfirm(false);
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
        $this->reassignConfirm();
        $html = new CStringBuilder();
        $html->setIndent($indent);
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
            $addition_attribute .= " " . $k . '="' . $v . '"';
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
        if ($this->confirm && !$this->submitTo) {
            $add_class .= " confirm";
        }
        if ($this->bootstrap == '3.3') {
            if (strlen($this->btn_style) == 0) {
                $add_class .= ' btn-' . $this->btn_style;
            }
        }
        if ($this->style == "btn-icon-group" && strlen($this->label) > 0) {
            $add_class .= " tip-top";
            $add_attr .= ' data-original-title="' . $this->label . '"';
        }
        if (strlen($this->confirm_message) > 0) {
            $add_attr .= ' data-confirm-message="' . base64_encode($this->confirm_message) . '"';
        }

        if ($this->render_as_input()) {
            $input_type = "button";

            if ($this->submit) {
                $input_type = "submit";
            }
            if ($this->button) {
                $html->appendln('<button id="' . $this->id . '" name="' . $this->id . '" class="btn btn-primary' . $add_class . $classes . '" type="' . $input_type . '"' . $disabled . $add_attr . $addition_attribute . $custom_css . '>' . $this->label . '</button>');
                if (strlen($this->icon) > 0) {
                    if ($this->bootstrap == '3.3') {
                        $html->append('<i class="fa fa-' . $this->icon . '"></i> ');
                    }
                }
                $html->appendln($this->label . '</button>');
            } else {
                $html->appendln('<button type="submit" id="' . $this->id . '" name="' . $this->id . '" class="btn btn-primary' . $add_class . $classes . '" type="' . $input_type . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . ' value="' . $this->label . '">' . $this->label . '</button>');
            }
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
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $link_target . ' class=" ' . $add_class . '' . $classes . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . '>');
            } else {
                $html->appendln('<a id="' . $this->id . '" href="' . $link . '"' . $link_target . ' class="btn ' . $add_class . '' . $classes . '" ' . $disabled . $add_attr . $addition_attribute . $custom_css . '>');
            }

            if (strlen($this->icon) > 0) {

                $html->append('<i class="icon icon-' . $this->getIcon() . ' ' . $this->getIcon() . '"></i> ');
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

        $js = new CStringBuilder();
        $js->setIndent($indent);

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
                if (strlen($this->submitTo) > 0) {
                    $jsSubmitToTarget = "";
                    if (strlen($this->submitToTarget) > 0) {
                        $jsSubmitToTarget = "jQuery('#" . $this->id . "').closest('form').attr('target','" . $this->submitToTarget . "');";
                    }
                    $this->addListener('click')->addHandler('custom')->setJs("
                        jQuery('#" . $this->id . "').closest('form').attr('action','" . $this->submitTo . "');
                        " . $jsSubmitToTarget . "
                        jQuery('#" . $this->id . "').closest('form').submit();
                       
                    ");
                }
            }
        }
        $this->reassignConfirm();

        $js->appendln(parent::jsChild($js->getIndent()))->br();

        return $js->text();
    }

}

<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 3, 2018, 2:34:55 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Password extends CElement_FormInput {

    use CTrait_Compat_Element_FormInput_Password,
        CTrait_Element_Property_Placeholder;

    protected $autocomplete;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "password";
        $this->autocomplete = true;
        $this->placeholder = "";
    }

    public static function factory($id) {
        return new CFormInputPassword($id);
    }

    public function set_autocomplete($bool) {
        $this->autocomplete = $bool;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);

        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        if ($this->bootstrap >= '3') {
            $classes = $classes . " form-control ";
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $additional_attr = ' autocomplete="off"';
        if ($this->autocomplete) {
            $additional_attr = ' autocomplete="on"';
        }
        $html->appendln('<input type="password" placeholder="' . $this->placeholder . '" name="' . $this->name . '" id="' . $this->id . '" ' . $custom_css . ' class="form-control input-unstyled' . $classes . $this->validation->validation_class() . '" value="' . $this->value . '" ' . $additional_attr . '>')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        return "";
    }

}

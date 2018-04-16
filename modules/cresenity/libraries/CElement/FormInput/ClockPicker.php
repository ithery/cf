<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CElement_FormInput_ClockPicker extends CElement_FormInput {

    protected $placeholder;
    protected $show_second;
    protected $template;
    protected $show_meridian;
    protected $minute_step;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = "clockpicker";
        $this->show_second = false;
        $this->template = 'dropdown';
        $this->show_meridian = false;
        $this->minute_step = 1;

        $this->placeholder = "";
        $dataModule = array(
            "css" => array(
                "plugins/clockpicker/jquery-clockpicker.css",
                "plugins/clockpicker/bootstrap-clockpicker.css",
            ),
            "js" => array(
                "plugins/clockpicker/jquery-clockpicker.js",
                "plugins/clockpicker/bootstrap-clockpicker.js",
            ),
        );
        CManager::registerModule('clockpicker', $dataModule);
    }

    public function set_placeholder($placeholder) {
        $this->placeholder = $placeholder;
        return $this;
    }

    public function set_show_meridian($bool) {
        $this->show_meridian = $bool;
        return $this;
    }

    public function set_show_second($bool) {
        $this->show_second = $bool;
        return $this;
    }

    public function set_minute_step($step) {
        $this->minute_step = $step;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $addition_attribute = "";
        foreach ($this->attr as $k => $v) {
            $addition_attribute .= " " . $k . '="' . $v . '"';
        }
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $placeholder = "";
        if (strlen($this->placeholder) > 0) {
            $placeholder = ' placeholder="' . $this->placeholder . '"';
        }


        $html->appendln('<div class="input-group clockpicker" data-autoclose="true">');
        $html->appendln('<input type="text" name="' . $this->name . '" id="' . $this->id . '" class="form-control input-unstyled ' . $classes . $this->validation->validation_class() . '" ' . $disabled . $custom_css . $placeholder . $addition_attribute . ' value="' . $this->value . '" >');
        $html->appendln('<span class="input-group-addon">');
        $html->appendln('<span class="fa fa-clock-o"></span>');
        $html->appendln('</span>');
        $html->appendln(' </div>');


        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);

        $js->appendln("$('#" . $this->id . "').clockpicker({");
        $js->appendln("donetext: 'OK'");

        $js->appendln("});");

        return $js->text();
    }

}

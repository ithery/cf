<?php

class CActionList extends CElement {

    public $actions = array();
    protected $style;
    protected $label;

    public function __construct($list_id) {
        parent::__construct($list_id);

        $this->style = "btn-list";
        $this->label = clang::__("Action");
    }

    public static function factory($list_id = "") {
        return new CActionList($list_id);
    }

    public function set_label($label) {
        $this->label = $label;
        return $this;
    }

    public function set_style($style) {

        if (in_array($style, array("form-action", "btn-group", "btn-icon-group", "btn-list", "icon-segment", "btn-dropdown", "widget-action", "table-header-action"))) {
            $this->style = $style;
        } else {
            trigger_error('style is not defined');
        }
        if ($this->id == "test-123") {
            //echo($this->style);
        }
        return $this;
    }

    public function html($indent = 0) {
        if ($this->id == "test-123") {
            //die($this->style);
        }
        //apply render style to child before render

        $this->apply('style', $this->style, 'CAction');
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $pretag = '<div class="button-list ' . $classes . '">';
        switch ($this->style) {
            case "form-action":
                $pretag = '<div class="form-actions clear-both ' . $classes . '">';
                break;
            case "btn-group":
            case "btn-icon-group":
                $pretag = '<div class="btn-group ' . $classes . '">';
                break;
            case "widget-action":
                $pretag = '<div class="buttons ' . $classes . '">';
                break;
			case "table-header-action":
                $pretag = '<div class="buttons ' . $classes . '">';
                break;
            case "btn-dropdown":
                $pretag = '<div class="btn-group ' . $classes . '">';
                break;
        }
        $html->appendln($pretag)->inc_indent()->br();
        if ($this->style == "btn-dropdown") {

            $html->appendln('
                    <a class="btn dropdown-toggle" data-toggle="dropdown" href="#">
                            ' . $this->label . '
                            <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu align-left ' . $classes . '">

            ');
        }
        $html->appendln(parent::html($html->get_indent()));
        if ($this->style == "btn-dropdown") {
            $html->appendln('</ul>');
        }
        $posttag = '</div>';
        switch ($this->style) {

            case "btn-dropdown":
                $posttag = "</div>";
                break;
            default:
                $posttag = "</div>";

                break;
        }
        $html->dec_indent()->appendln($posttag)->br();
        return $html->text();
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->appendln(parent::js($js->get_indent()));
        return $js->text();
    }

}

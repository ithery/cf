<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Feb 16, 2018, 6:10:37 AM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_List_ActionList extends CElement_List {

    use CTrait_Compat_Element_ActionList;

    public $actions = array();
    protected $style;
    protected $label;
    protected $btn_dropdown_classes;
    protected $label_size;

    public function __construct($list_id) {
        parent::__construct($list_id);

        $this->style = "btn-list";
        $this->label = clang::__("Action");
        $this->btn_dropdown_classes = array();
        $this->label_size = 2;
    }

    public static function factory($list_id = "") {
        return new CElement_List_ActionList($list_id);
    }

    public function set_label($label) {
        $this->label = $label;
        return $this;
    }

    public function set_label_size($label_size) {
        $this->label_size = $label_size;
        return $this;
    }

    public function add_btn_dropdown_class($class) {
        $this->btn_dropdown_classes[] = $class;
        return $this;
    }

    public function setStyle($style) {

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

    public function getStyle() {
        return $this->style;
    }

    public function html($indent = 0) {
        if ($this->id == "test-123") {
            //die($this->style);
        }
        //apply render style to child before render

        if (count($this->btn_dropdown_classes) == 0) {
            if ($this->bootstrap >= '3') {
                $this->btn_dropdown_classes[] = 'btn-primary';
                $this->btn_dropdown_classes[] = 'btn-sm';
            }
        }

        $this->apply('style', $this->style, 'CElement_Component_Action');
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0) {
            $classes = " " . $classes;
        }
        $btn_dropdown_classes = $this->btn_dropdown_classes;
        $btn_dropdown_classes = implode(" ", $btn_dropdown_classes);
        if (strlen($btn_dropdown_classes) > 0) {
            $btn_dropdown_classes = " " . $btn_dropdown_classes;
        }
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $pretag = '<div id="' . $this->id . '" class="button-list ' . $classes . '">';
        switch ($this->style) {
            case "form-action":
                if ($this->bootstrap == '3.3') {
                    $control_size = 12 - $this->label_size;
                    $pretag = '
                        <div class="form-group clear-both ' . $classes . '">
                            <label class="col-md-' . $this->label_size . ' control-label"></label>
                                <div class="col-md-' . $control_size . '">
                            ';
                } else {
                    $pretag = '<div class="form-actions clear-both ' . $classes . '">';
                }
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
                    <a class="btn ' . $btn_dropdown_classes . ' dropdown-toggle" data-toggle="dropdown" href="#">
                            ' . $this->label . '
                            <span class="caret"></span>
                    </a>
                    <ul class="dropdown-menu align-left ' . $classes . '">

            ');
        }

        $html->appendln($this->html_child($html->get_indent()));


        if ($this->style == "btn-dropdown") {
            $html->appendln('</ul>');
        }
        $posttag = '</div>';
        switch ($this->style) {

            case "btn-dropdown":
                $posttag = "</div>";
                break;
            case "form-action":
                if ($this->bootstrap == '3.3') {
                    $posttag = "</div></div>";
                } else {
                    $posttag = "</div>";
                }
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
        $js->appendln($this->js_child($js->get_indent()));
        return $js->text();
    }

}

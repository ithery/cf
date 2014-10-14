<?php

class CFormInputWysiwyg extends CFormInputTextarea {

    protected $col;
    protected $row;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = "wysiwyg";
        $this->col = 60;
        $this->row = 10;
    }

    public static function factory($id) {
        return new CFormInputWysiwyg($id);
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;
        $custom_css = $this->custom_css;
        $custom_css = crenderer::render_style($custom_css);
        if (strlen($custom_css) > 0) {
            $custom_css = ' style="' . $custom_css . '"';
        }
        $disabled = "";
        if ($this->disabled)
            $disabled = ' disabled="disabled"';
        $html->appendln('<textarea cols="' . $this->col . '" rows="' . $this->row . '" name="' . $this->name . '" id="' . $this->id . '" class="wysiwyg' . $this->validation->validation_class() . $classes . '" ' . $disabled . $custom_css . '>' . $this->value . '</textarea>')->br();
        //$html->appendln('<input type="text" name="'.$this->name.'" id="'.$this->id.'" class="input-unstyled'.$this->validation->validation_class().'" value="'.$this->value.'"'.$disabled.'>')->br();
        return $html->text();
    }

    public function js($indent = 0) {

        $js = new CStringBuilder();
        $js->set_indent($indent);
        $js->append("$('#" . $this->id . "').wysihtml5(")->br();
        $js->append("{")->br();
        $js->append("'link':false,")->br();
        $js->append("'image':false")->br();
        $js->append("}")->br();

        $js->append(");")->br();

        return $js->text();
    }

}
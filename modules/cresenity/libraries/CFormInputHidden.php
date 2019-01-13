<?php

class CFormInputHidden extends CFormInput {

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "text";
    }

    public static function factory($id = '') {
        return new CFormInputHidden($id);
    }

    public function toarray() {
        $data = array();
        $data['attr']['type'] = "hidden";
        $data['attr']['value'] = $this->value;

        $data = array_merge_recursive($data, parent::toarray());
        return $data;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);
        $classes = $this->classes;
        $classes = implode(" ", $classes);
        if (strlen($classes) > 0)
            $classes = " " . $classes;

        $attr = '';
        foreach ($this->attr as $k => $v) {
            $attr .= $k . '="' . $v . '" ';
        }

        $html->appendln('<input type="hidden" name="' . $this->name . '" id="' . $this->id . '" class="' . $classes . '" ' . $attr . ' value="' . $this->value . '">')->br();
        return $html->text();
    }

    public function js($indent = 0) {
        return "";
    }

}

<?php

class CFormFieldSet extends CRenderable {

    protected $legend;

    public function __construct($id) {
        parent::__construct($id);

        $this->legend = "";
    }

    public static function factory($id) {
        return new CFormFieldSet($id);
    }

    public function add_field($id = '') {
        $formfield = CFormField::factory($id);
        $this->add($formfield);
        return $formfield;
    }
	
	public function set_legend($legend) {
        $this->legend = $legend;
		return $this;
	}

    public function html($indent = 0) {
        $html = new CStringBuilder();
        $html->set_indent($indent);


        $addition_str = "";

        $html->appendln('<fieldset id="' . $this->id . '">');

        $html->appendln('<legend>' . $this->legend . '</legend>');
        $html->appendln(parent::html($html->get_indent()));


        $html->dec_indent()
                ->appendln('</fieldset>');
        return $html->text();
    }

}

?>
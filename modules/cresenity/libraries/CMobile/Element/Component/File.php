<?php

defined('SYSPATH') OR die('No direct access allowed.');

class CMobile_Element_Component_File extends CMobile_Element_AbstractComponent {

    public function __construct($id = "") {

        parent::__construct($id);
        $this->tag = "div";
    }

    public static function factory($id = "") {
        return new CMobile_Element_Component_File($id);
    }


	public function html($indent=0) {
		$this->add_class('btn');
		$html = new CStringBuilder();
        $html->set_indent($indent);
        $html->appendln('<div class="file-field input-field">');
        $html->appendln('	<div class="btn">
						        <span>File</span>
						        <input type="file" id="' . $this->id . '">
					      	</div>
					      	<div class="file-path-wrapper">
						        <input class="file-path validate" type="text">
					      	</div>');
        $html->appendln('</div>');
        return $html->text();
	}

	public function js($indent=0) {
		
		$js = "
		";
		$js.=parent::js();
		return $js;
	}
}

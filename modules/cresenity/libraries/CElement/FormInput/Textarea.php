<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Textarea
 *
 * @author Hery Kurniawan
 * @since Jan 28, 2018, 9:50:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Textarea extends CElement_FormInput {

    use CTrait_Compat_Element_FormInput_Textarea;
    
    protected $col;
    protected $row;
    protected $placeholder;

    //put your code here
    public function __construct($id) {
        parent::__construct($id);

        $this->tag = "textarea";
        $this->is_onetag = false;
        $this->addClass('form-control');
        $this->placeholder = "";
        $this->col = 60;
        $this->row = 10;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();

        $html->set_indent($indent);
        $this->build_once();
        $html->appendln($this->before_html($indent));

        $html->appendln($this->pretag());
        $html->appendln($this->value);
        $html->appendln($this->posttag());

        $html->appendln($this->after_html($indent));

        return $html->text();
    }

    public function setCol($col) {
        $this->col = $col;
        return $this;
    }

    public function setRow($row) {
        $this->row = $row;
        return $this;
    }

}

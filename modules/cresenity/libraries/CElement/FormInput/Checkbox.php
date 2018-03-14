<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Checkbox
 *
 * @author Alvin
 * @since Mar 14, 2018, 15:35:24 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_Checkbox extends CElement_FormInput {

    protected $checked;

    //put your code here
    public function __construct($id) {
        parent::__construct($id);

        $this->type = "checkbox";
        $this->tag = "input";
        $this->is_onetag = true;
        $this->checked = false;
    }

    public function onetag() {
        return '<' . $this->tag . ' type="' . $this->type . '"  ' . $this->html_attr() . ' ' . (($this->checked) ? 'checked ' : '') . '/>';
    }

    public function pretag() {
        return '<' . $this->tag . ' type="' . $this->type . '"  ' . $this->html_attr() . ' ' . (($this->checked) ? 'checked ' : '') . '>';
    }

    public function posttag() {
        return '</' . $this->tag . '>';
    }

    public function setChecked($bool)
    {
        $this->checked = $bool;
        return $this;
    }

    public function html($indent = 0) {
        $html = new CStringBuilder();

        $html->set_indent($indent);
        $this->build_once();
        $html->appendln($this->before_html($indent));

        if ($this->is_onetag) {
            $html->appendln($this->onetag());
            $html->appendln($this->value);
        } else {
            $html->appendln($this->pretag());
            $html->appendln($this->value);
            $html->appendln($this->posttag());
        }

        $html->appendln($this->after_html($indent));

        return $html->text();
    }

}

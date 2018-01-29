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

    //put your code here
    public function __construct($id) {
        parent::__construct($id);

        $this->tag = "textarea";
        $this->is_onetag = false;
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

}

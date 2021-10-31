<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Apr 21, 2019, 5:58:13 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_MiniColor extends CElement_FormInput {

    use CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);
        $this->type = "text";
        $this->addClass('form-control');
        $this->placeholder='';
        CManager::registerModule('minicolors');    }
    
    public function build() {
        $this->setAttr('placeholder',$this->placeholder);
        $this->setAttr('value',$this->value);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $miniColorOptions = array();
        $miniColorOptions['control'] = 'hue';
        $miniColorOptions['position'] = 'bottom left';
        $miniColorJsonOptions = json_encode($miniColorOptions);
        $miniColorJs = "$('#" . $this->id . "').minicolors(" . $miniColorJsonOptions . ")";
        $js->appendln($miniColorJs);
        $js->append(parent::js());


        return $js->text();
    }

}

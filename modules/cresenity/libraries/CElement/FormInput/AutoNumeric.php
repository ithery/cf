<?php

defined('SYSPATH') OR die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @since Jun 24, 2018, 6:26:04 PM
 * @license Ittron Global Teknologi <ittron.co.id>
 */
class CElement_FormInput_AutoNumeric extends CElement_FormInput {

    use CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = "text";
        $this->placeholder = "";
        $this->value = "0";
        $this->addClass('form-control');

        if (!CManager::asset()->module()->isRegisteredModule('auto-numeric')) {
            CManager::asset()->module()->registerRunTimeModule('auto-numeric');
        }
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::jsChild());

        $js->append("
            new AutoNumeric('#" . $this->id . "', {});
        ");

        return $js->text();
    }

}

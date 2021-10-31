<?php

defined('SYSPATH') or die('No direct access allowed.');

/**
 * @author Hery Kurniawan
 * @license Ittron Global Teknologi <ittron.co.id>
 *
 * @since Jun 24, 2018, 6:26:04 PM
 */
class CElement_FormInput_Currency extends CElement_FormInput {
    use CTrait_Compat_Element_FormInput_Currency,
        CTrait_Element_Property_Placeholder;

    public function __construct($id) {
        parent::__construct($id);

        $this->type = 'text';
        $this->placeholder = '';
        $this->value = '0';
        $this->addClass('form-control');
    }

    protected function build() {
        $this->setAttr('type', $this->type);
        $this->setAttr('value', $this->value);
    }

    public function js($indent = 0) {
        $js = new CStringBuilder();
        $js->setIndent($indent);
        $js->append(parent::jsChild());

        $js->append("$('#" . $this->id . "').focus( function() {
				$('#" . $this->id . "').val($.cresenity.unformat_currency($('#" . $this->id . "').val()))
			});")->br();
        $js->append("$('#" . $this->id . "').blur(function() {
				$('#" . $this->id . "').val($.cresenity.format_currency($('#" . $this->id . "').val()))
			});")->br();

        return $js->text();
    }
}

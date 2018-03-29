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
class CElement_FormInput_Checkbox_Switcher extends CElement_FormInput_Checkbox {

    public function __construct($id) {
        parent::__construct($id);

        $this->custom_css('display', 'none');
    }

    public function build() {
        $this->add_class('switcher-control');
    }

    public function html($indent = 0)
    {
        $html = '<div class="switcher">';

        $html .= parent::html();

        $html .= '
            <label class="switcher-label" for="' . $this->id . '">
                <span class="switcher-inner"></span>
                <span class="switcher-switch"></span>
            </label>
        ';

        $html .= '</div>';

        return $html;
    }

    public function js($indent = 0) {

        $js = "";

        $js .= '

            var input = $("#' . $this->id . '");
            var label = input.next()[0];

            $(label).click(function() {
                if (input.prop("checked")) {
                    input.removeAttr("checked");
                } else {
                    input.prop("checked", true);
                }
            });
      
        ';

        $js .= parent::js();
        return $js;
    }

}

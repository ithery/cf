<?php

class CElement_FormInput_Checkbox_Switcher extends CElement_FormInput_Checkbox {
    public function __construct($id) {
        parent::__construct($id);
    }

    public function build() {
        $this->addClass('switcher-control');
    }

    public function html($indent = 0) {
        $checked = '';
        if ($this->checked) {
            $checked = ' checked="checked"';
        }

        $html = '<div class="switcher">';

        // $html .= parent::html();
        $html .= '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="switcher-control ' . $this->validation->validationClass() . '"' . $checked . ' style="display:none">';

        $html .= '
            <label class="switcher-label" for="' . $this->id . '">
                <span class="switcher-inner"></span>
                <span class="switcher-switch"></span>
            </label>
        ';

        $html .= '</div>';

        return $html;
    }
}

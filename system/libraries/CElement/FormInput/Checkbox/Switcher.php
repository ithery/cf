<?php

class CElement_FormInput_Checkbox_Switcher extends CElement_FormInput_Checkbox {
    /**
     * @param string $id
     *
     * @return void
     */
    public function __construct($id) {
        parent::__construct($id);
    }

    /**
     * @return void
     */
    public function build() {
        $this->addClass('switcher-control');
    }

    /**
     * @param int $indent
     *
     * @return string
     */
    public function html($indent = 0) {
        $checked = '';
        if ($this->checked) {
            $checked = ' checked="checked"';
        }

        $html = '<div class="switcher">';

        // $html .= parent::html();
        $html .= '<input type="checkbox" name="' . $this->name . '" id="' . $this->id . '" class="switcher-control ' . '"' . $checked . ' style="display:none">';

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

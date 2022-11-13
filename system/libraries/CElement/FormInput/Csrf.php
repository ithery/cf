<?php

class CElement_FormInput_Csrf extends CElement_FormInput_Hidden {
    public function __construct($id) {
        parent::__construct($id);

        $this->value = c::csrfToken();
        $this->name = '_token';
    }

    public static function factory($id = null) {
        return new CElement_FormInput_Csrf($id);
    }
}
